<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts;

use Apps\Dash\Packages\Business\Directory\Contacts\Contacts as BusinessDirectoryContacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendorsFinancialDetails;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Crms\Customers\Customers;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Attachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsAddresses;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsContactPersons;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsFinance;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\SyncCustomers;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\SyncVendors;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\History;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $syncDataDirectory = 'var/api/sync/contacts/';

    protected $scheduleChildrenTasks = false;

    protected $vendorsPackage;

    protected $contactsPackage;

    protected $customersPackage;

    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    protected $downloadCounter = 0;

    protected $downloadIds = [];

    protected $addCounter = 0;

    protected $addedIds = [];

    protected $updateCounter = 0;

    protected $updatedIds = [];

    protected $skippedCounter = 0;

    protected $skippedIds = [];

    protected $responseData = [];

    protected $responseMessage = '';

    public $processing = null;

    protected function getEnabledApis()
    {
        if (!$this->apiPackage) {
            $this->apiPackage = new Api;
        }

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis) {
            if ($xeroApis && count($xeroApis) > 0) {
                return $xeroApis;
            }
        }

        return false;
    }

    public function sync($apiId = null, $parameters = null)
    {
        $this->request = new GetContactsRestRequest;

        $xeroApis = $this->getEnabledApis();

        if ($xeroApis) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id'], $parameters);
                }
            } else {
                $this->syncWithXero($apiId, $parameters);
            }

            $this->responseData = array_merge($this->responseData,
                [
                    'downloadIds'   => $this->downloadIds,
                    'skippedIds'    => $this->skippedIds
                ]
            );

            $this->responseMessage =
                $this->responseMessage . ' ' . 'Contacts Sync Ok. Downloaded: ' . $this->downloadCounter . '. Skipped: ' . $this->skippedCounter . '.';

            $this->addResponse(
                $this->responseMessage,
                0,
                $this->responseData
            );
        } else {
            $this->addResponse('Sync Error. No API Configuration Found', 1);
        }

        return $this->scheduleChildrenTasks;
    }

    protected function syncWithXero($apiId, $parameters = null)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        if ($parameters && isset($parameters[$apiId]['Contacts']['ContactID'])) {
            $request = new GetContactRestRequest;

            $contacts = [];

            if (is_string($parameters[$apiId]['Contacts']['ContactID'])) {
                array_push($contacts, $parameters[$apiId]['Contacts']['ContactID']);
            } else {
                $contacts = $parameters[$apiId]['Contacts']['ContactID'];
            }

            foreach ($contacts as $contact) {
                $request->ContactID = $contact;

                $response = $this->xeroApi->getContact($request);

                $this->api->refreshXeroCallStats($response->getHeaders());

                if ($response->getStatusCode() === 404) {
                    $this->skippedIds[$contact] = 'Skipped - Error: Not Found!';

                    $this->skippedCounter = $this->skippedCounter + 1;

                    continue;
                }

                $responseArr = $response->toArray();

                if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
                    isset($responseArr['Contacts'])
                ) {
                    if (count($responseArr['Contacts']) > 0) {
                        $this->scheduleChildrenTasks = true;

                        $this->syncData($apiId, $responseArr['Contacts']);

                        $this->downloadCounter = $this->downloadCounter + count($responseArr['Contacts']);
                    }
                }
            }

            return;
        }

        if ($parameters && isset($parameters[$apiId]['Contacts']['modifiedSince'])) {
            $modifiedSince = $parameters[$apiId]['Contacts']['modifiedSince'];//Should be set to UTC
        } else {
            $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetContacts', $apiId);
        }

        if ($modifiedSince) {
            $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        $page = 1;
        $continue = true;

        if ($parameters && isset($parameters[$apiId]['Contacts']['startPage'])) {
            $page = (int) $parameters[$apiId]['Contacts']['startPage'];
        } else if ($parameters && isset($parameters[$apiId]['Contacts']['page'])) {
            $page = (int) $parameters[$apiId]['Contacts']['page'];
            $continue = false;
        }

        do {
            //We sleep for a second between page grabs
            sleep(1);

            $this->request->page = $page;

            $this->request->includeArchived = true;

            $response = $this->xeroApi->getContacts($this->request);

            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
                isset($responseArr['Contacts'])
            ) {
                if (count($responseArr['Contacts']) > 0) {
                    $this->scheduleChildrenTasks = true;

                    $this->syncData($apiId, $responseArr['Contacts']);

                    $this->downloadCounter = $this->downloadCounter + count($responseArr['Contacts']);
                }
            }

            if ($parameters && isset($parameters[$apiId]['Contacts']['endPage'])) {
                if ($page !== $parameters[$apiId]['Contacts']['endPage'])  {
                    $page++;
                } else {
                    $continue = false;
                }
            } else {
                $page++;
            }

        } while ($continue && isset($responseArr['Contacts']) && count($responseArr['Contacts']) > 0);
    }

    protected function syncData($apiId, array $contacts)
    {
        foreach ($contacts as $contactKey => $contact) {
            $contact['lock'] = false;
            $contact['api_id'] = $apiId;

            $this->writeJsonFile($contact);
        }
    }

    protected function writeJsonFile($contact)
    {
        $fileName = $contact['ContactID'] . '.json';

        array_push($this->downloadIds, $contact['ContactID']);

        $contact = Json::encode($contact);

        $this->localContent->write($this->syncDataDirectory . $fileName, $contact);
    }

    public function syncFromData($apiId = null, $parameters = null, $count = 50)
    {
        if (!$this->getEnabledApis()) {
            $this->addResponse('Sync Error. No API Configuration Found', 1);

            return false;
        }

        if (isset($parameters['processCount'])) {
            $count = $parameters['processCount'];
        }

        $contact = null;

        $files =
            $this->localContent->listContents($this->syncDataDirectory, false)
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();

        if ($files && is_array($files) && count($files) > 0) {
            if (count($files) < $count) {
                $count = count($files);

                $this->scheduleChildrenTasks = false;
            } else {
                $this->scheduleChildrenTasks = true;
            }

            for ($fileCount = 0; $fileCount < $count; $fileCount++) {
                try {
                    $contact = $this->localContent->read($files[$fileCount]);

                    $contact = Json::decode($contact, true);

                    if ($contact['lock'] === true) {//Lock in case multiple process access the same file.
                        continue;
                    }

                    $contact['lock'] = true;

                    $this->writeJsonFile($contact);

                    if (!$this->api) {
                        $this->api = $this->apiPackage->useApi(['api_id' => $contact['api_id']]);
                    }

                    if (!$this->xeroApi) {
                        $this->xeroApi = $this->api->useService('XeroAccountingApi');
                    }

                    try {
                        if ($this->addUpdateXeroContacts($contact['api_id'], $contact)) {
                            try {
                                $this->localContent->delete($files[$fileCount]);
                            } catch (FilesystemException | UnableToDeleteFile $exception) {
                                throw $exception;
                            }
                        }
                    } catch (\PDOException | \Exception $e) {
                        if (get_class($e) !== 'PDOException') {
                            throw $e;
                        }

                        $this->skippedIds[$contact['ContactID']] = 'Skipped - Error: ' . $this->escaper->escapeHtml($e->getMessage());
                    }
                } catch (FilesystemException | UnableToReadFile $exception) {
                    throw $exception;
                }
            }
        }

        try {
            $this->syncWithLocal();
        } catch (\PDOException | \Exception $e) {
            if (get_class($e) !== 'PDOException') {
                throw $e;
            }

            $this->skippedIds[$contact['ContactID']] = 'Skipped - Error: ' . $this->escaper->escapeHtml($e->getMessage());
        }

        $this->responseData = array_merge($this->responseData,
            [
                'addedIds'      => $this->addedIds,
                'updatedIds'    => $this->updatedIds,
                'skippedIds'    => $this->skippedIds
            ]
        );

        $this->responseMessage =
            $this->responseMessage . ' ' .
            'Contacts Sync Ok. Added: ' . $this->addCounter .
            '. Updated: ' . $this->updateCounter .
            '. Skipped: ' . $this->skippedCounter . '.';

        $this->addResponse(
            $this->responseMessage,
            0,
            $this->responseData
        );

        return $this->scheduleChildrenTasks;
    }

    protected function addUpdateXeroContacts($apiId, $contact)
    {
        $this->processing = $contact['ContactID'];

        $model = SystemApiXeroContacts::class;

        $xeroContact = $model::findFirst(
            [
                'conditions'    => 'ContactID = :cid:',
                'bind'          =>
                    [
                        'cid'   => $contact['ContactID']
                    ]
            ]
        );

        $contact['api_id'] = $apiId;

        if (!$xeroContact) {
            $modelToUse = new $model();

            $modelToUse->assign($this->jsonData($contact));

            $modelToUse->create();

            $this->addCounter = $this->addCounter + 1;

            $this->addedIds[$contact['ContactID']] = 0;

            $thisContact = $modelToUse->toArray();
        } else {
            if ($xeroContact->baz_vendor_id) {
                $contact['resync_local'] = '1';
            }

            $xeroContact->assign($this->jsonData($contact));

            $xeroContact->update();

            $this->updateCounter = $this->updateCounter + 1;

            $this->updatedIds[$contact['ContactID']] = 0;

            $thisContact = $xeroContact->toArray();
        }

        if (isset($contact['Addresses']) && count($contact['Addresses']) > 0) {
            $this->addUpdateXeroContactsAddresses($thisContact, $contact['Addresses']);
        }

        if (isset($contact['Phones']) && count($contact['Phones']) > 0) {
            $this->addUpdateXeroContactsPhones($thisContact, $contact['Phones']);
        }

        if (isset($contact['ContactPersons']) && count($contact['ContactPersons']) > 0) {
            $this->addUpdateXeroContactsContactPersons($thisContact, $contact['ContactPersons']);
        }

        $this->addUpdateXeroContactsFinance($thisContact, $contact);

        //We dont want to make additional calls for Mr Nobody!.
        //@todo - This can be done on demand from the customer/accounting app.
        if ($contact['IsCustomer'] == '0' && $contact['IsSupplier'] == '0') {
            return true;
        } else if ($contact['IsCustomer'] == '1') {
            if (!isset($contact['EmailAddress']) ||
                (isset($contact['EmailAddress']) && $contact['EmailAddress'] === '')
            ) {
                return true;
            }
        }

        if (isset($contact['HasAttachments']) && $contact['HasAttachments'] == true) {
            $xeroAttachments = new Attachments;

            $xeroAttachments->sync(
                $apiId,
                $this->packageName,
                $thisContact['ContactID'],
                $this->getContactsAttachments($thisContact['ContactID'])
            );
        }

        $xeroHistory = new History;

        $xeroHistory->sync(
            $apiId,
            $this->packageName,
            $thisContact['ContactID'],
            $this->getContactsHistory($thisContact['ContactID'])
        );

        return true;
    }

    protected function getContactsAttachments($contactId)
    {
        $request = new GetContactAttachmentsRestRequest;

        $request->ContactID = $contactId;

        $response = $this->xeroApi->getContactAttachments($request);

        $this->api->refreshXeroCallStats($response->getHeaders());

        $responseArr = $response->toArray();

        if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
            if (isset($responseArr['Attachments'])) {
                return $responseArr['Attachments'];
            }
        }

        return [];
    }

    protected function getContactsHistory($contactId)
    {
        $request = new GetContactHistoryRestRequest;

        $request->ContactID = $contactId;

        $response = $this->xeroApi->getContactHistory($request);

        $this->api->refreshXeroCallStats($response->getHeaders());

        $responseArr = $response->toArray();

        if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
            if (isset($responseArr['HistoryRecords'])) {
                return $responseArr['HistoryRecords'];
            }
        }

        return [];
    }

    protected function addUpdateXeroContactsAddresses($contact, $addresses)
    {
        $model = SystemApiXeroContactsAddresses::class;

        foreach ($addresses as $addressKey => $address) {
            $xeroContactAddress = $model::findFirst(
                [
                    'conditions'    => 'ContactID = :cid: AND AddressType = :at:',
                    'bind'          =>
                        [
                            'cid'   => $contact['ContactID'],
                            'at'    => $address['AddressType']
                        ]
                ]
            );

            $address['ContactID'] = $contact['ContactID'];

            if (!$xeroContactAddress) {
                $modelToUse = new $model();

                $modelToUse->assign($address);

                $modelToUse->create();
            } else {
                $xeroContactAddress->assign($address);

                $xeroContactAddress->update();
            }
        }
    }

    protected function addUpdateXeroContactsPhones($contact, $phones)
    {
        $model = SystemApiXeroContactsPhones::class;

        foreach ($phones as $phoneKey => $phone) {
            $xeroContactPhone = $model::findFirst(
                [
                    'conditions'    => 'ContactID = :cid: AND PhoneType = :pt:',
                    'bind'          =>
                        [
                            'cid'   => $contact['ContactID'],
                            'pt'    => $phone['PhoneType']
                        ]
                ]
            );

            $phone['ContactID'] = $contact['ContactID'];

            if (!$xeroContactPhone) {
                $modelToUse = new $model();

                $modelToUse->assign($phone);

                $modelToUse->create();
            } else {
                $xeroContactPhone->assign($phone);

                $xeroContactPhone->update();
            }
        }
    }

    protected function addUpdateXeroContactsContactPersons($contact, $contactPersons)
    {
        $model = SystemApiXeroContactsContactPersons::class;

        foreach ($contactPersons as $contactPersonKey => $contactPerson) {
            $xeroContactContactPersons = $model::findFirst(
                [
                    'conditions'    => 'ContactID = :cid: AND EmailAddress = :email:',
                    'bind'          =>
                        [
                            'cid'   => $contact['ContactID'],
                            'email' => $contactPerson['EmailAddress']
                        ]
                ]
            );

            $contactPerson['ContactID'] = $contact['ContactID'];

            if (!$xeroContactContactPersons) {
                $modelToUse = new $model();

                $modelToUse->assign($contactPerson);

                $modelToUse->create();
            } else {
                $contactPerson['resync_local'] = '1';

                $xeroContactContactPersons->assign($contactPerson);

                $xeroContactContactPersons->update();
            }
        }
    }

    protected function addUpdateXeroContactsFinance($contact, $finance)
    {
        $model = SystemApiXeroContactsFinance::class;

        $xeroContactFinance = $model::findFirst(
            [
                'conditions'    => 'ContactID = :cid:',
                'bind'          =>
                    [
                        'cid'   => $contact['ContactID']
                    ]
            ]
        );

        $finance['ContactID'] = $contact['ContactID'];

        if (isset($finance['PaymentTerms']) && count($finance['PaymentTerms']) > 0) {
            if (isset($finance['PaymentTerms'][0])) {
                $finance['PaymentTermsBillsDay'] = $finance['PaymentTerms'][0]['Day'];
                $finance['PaymentTermsBillsType'] = $finance['PaymentTerms'][0]['Type'];
            }
            if (isset($finance['PaymentTerms'][1])) {
                $finance['PaymentTermsSalesDay'] = $finance['PaymentTerms'][1]['Day'];
                $finance['PaymentTermsSalesType'] = $finance['PaymentTerms'][1]['Type'];
            }
        }

        if (!$xeroContactFinance) {
            $modelToUse = new $model();

            $modelToUse->assign($this->jsonData($finance));

            $modelToUse->create();
        } else {
            $xeroContactFinance->assign($this->jsonData($finance));

            $xeroContactFinance->update();
        }
    }

    public function syncWithLocal()
    {
        $this->processing = null;

        $model = SystemApiXeroContacts::class;

        $xeroContact = $model::find(
            [
                'conditions'    => 'baz_vendor_id IS NULL AND baz_customer_id IS NULL OR resync_local = :rl:',
                'bind'          =>
                    [
                        'rl'    => '1',
                    ]
            ]
        );

        if ($xeroContact) {
            $this->vendorsPackage = $this->usePackage(Vendors::class);

            $this->contactsPackage = $this->usePackage(BusinessDirectoryContacts::class);

            $this->customersPackage = $this->usePackage(Customers::class);

            $contacts = $xeroContact->toArray();

            $syncCustomers = (new SyncCustomers)->setup($this->api, $this->xeroApi);

            $syncVendors = (new SyncVendors)->setup($this->api, $this->xeroApi);

            if ($contacts && count($contacts) > 0) {
                foreach ($contacts as $contactKey => $contact) {
                    if ($contact['IsCustomer'] == '0' && $contact['IsSupplier'] == '0') {
                        if (isset($this->addedIds[$contact['ContactID']])) {
                            unset($this->addedIds[$contact['ContactID']]);
                            $this->addCounter = $this->addCounter - 1;
                        } else if (isset($this->updatedIds[$contact['ContactID']])) {
                            unset($this->updatedIds[$contact['ContactID']]);
                            $this->updateCounter = $this->updateCounter - 1;
                        }

                        $this->skippedIds[$contact['ContactID']] = 'Skipped - Contact is not customer or supplier as per xero.';

                        $this->skippedCounter = $this->skippedCounter + 1;

                        $markContactObj = $model::findById($contact['id']);

                        if ($markContactObj) {
                            $markContact = $markContactObj->toArray();

                            $markContact['baz_vendor_id'] = 0;
                            $markContact['baz_customer_id'] = 0;

                            $markContactObj->update($markContact);
                        }

                        continue;//We dont want to add just placeholder accounts.
                    }

                    $this->processing = $contact['ContactID'];

                    $addressesModel = SystemApiXeroContactsAddresses::class;

                    $contactAddresses = $addressesModel::find(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );
                    if ($contactAddresses) {
                        $contact['addresses'] = $contactAddresses->toArray();
                    }

                    $contactPersonsModel = SystemApiXeroContactsContactPersons::class;
                    $contactPersons = $contactPersonsModel::find(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );
                    if ($contactPersons) {
                        $contact['persons'] = $contactPersons->toArray();
                    }

                    $contactFinanceModel = SystemApiXeroContactsFinance::class;
                    $contactFinance = $contactFinanceModel::findFirst(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );
                    if ($contactFinance) {
                        $contact['finance'] = $contactFinance->toArray();
                    }

                    $contactPhonesModel = SystemApiXeroContactsPhones::class;
                    $contactPhones = $contactPhonesModel::find(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );
                    if ($contactPhones) {
                        $contact['phones'] = $contactPhones->toArray();
                    }

                    if ($contact['IsCustomer'] == '1' && $contact['IsSupplier'] == '0') {
                        $customer = $syncCustomers->generateCustomerData($contact);

                        if ($customer['customer']['id'] != '0') {
                            if (isset($this->addedIds[$customer['customer']['ContactID']])) {
                                $this->addedIds[$customer['customer']['ContactID']] = 'Created customer with ID: ' . $customer['customer']['id'];
                            } else if (isset($this->updatedIds[$customer['customer']['ContactID']])) {
                                $this->updatedIds[$customer['customer']['ContactID']] = 'Updated Customer with ID: ' . $customer['customer']['id'];
                            }

                            if ($customer['errors']['customers'] && count($customer['errors']['customers']) > 0) {
                                if ($customer['errors']['address'] && count($customer['errors']['address']) > 0) {
                                    $errors = array_merge($customer['errors']['customers'], $customer['errors']['address']);
                                } else {
                                    $errors = $customer['errors']['customers'];
                                }
                                $this->customersPackage->errorCustomer('Errors in customers. Please check details for more information.', Json::encode($errors));
                            }
                        } else {
                            if (isset($this->addedIds[$customer['customer']['ContactID']])) {
                                unset($this->addedIds[$customer['customer']['ContactID']]);
                                $this->addCounter = $this->addCounter - 1;
                            } else if (isset($this->updatedIds[$customer['customer']['ContactID']])) {
                                unset($this->updatedIds[$customer['customer']['ContactID']]);
                                $this->updateCounter = $this->updateCounter - 1;
                            }

                            $this->skippedIds[$customer['customer']['ContactID']] = 'Skipped - Contact email is not set.';

                            $this->skippedCounter = $this->skippedCounter + 1;

                            $markContactObj = $model::findByContactID($customer['customer']['ContactID']);

                            if ($markContactObj) {
                                $markContact = $markContactObj->toArray();

                                $markContact['baz_vendor_id'] = 0;
                                $markContact['baz_customer_id'] = 0;

                                $markContactObj->update($markContact);
                            }
                        }
                    } else if (($contact['IsCustomer'] == '0' && $contact['IsSupplier'] == '1') ||
                               ($contact['IsCustomer'] == '1' && $contact['IsSupplier'] == '1')
                    ) {
                        $vendor = $syncVendors->generateVendorData($contact);

                        if ($vendor['vendor']['id'] != '0') {
                            if (isset($this->addedIds[$vendor['vendor']['ContactID']])) {
                                $this->addedIds[$vendor['vendor']['ContactID']] = 'Created vendor with ID: ' . $vendor['vendor']['id'];
                            } else if (isset($this->updatedIds[$vendor['vendor']['ContactID']])) {
                                $this->updatedIds[$vendor['vendor']['ContactID']] = 'Updated vendor with ID: ' . $vendor['vendor']['id'];
                            }

                            if ($vendor['errors']['vendors'] && count($vendor['errors']['vendors']) > 0) {
                                if ($vendor['errors']['address'] && count($vendor['errors']['address']) > 0) {
                                    $errors = array_merge($vendor['errors']['vendors'], $vendor['errors']['address']);
                                } else {
                                    $errors = $vendor['errors']['vendors'];
                                }
                                $this->vendorsPackage->errorVendor('Errors in vendors. Please check details for more information.', Json::encode($errors));
                            }
                        } else {
                            if (isset($this->addedIds[$vendor['vendor']['ContactID']])) {
                                unset($this->addedIds[$vendor['vendor']['ContactID']]);
                                $this->addCounter = $this->addCounter - 1;
                            } else if (isset($this->updatedIds[$vendor['vendor']['ContactID']])) {
                                unset($this->updatedIds[$vendor['vendor']['ContactID']]);
                                $this->updateCounter = $this->updateCounter - 1;
                            }

                            $this->skippedIds[$vendor['vendor']['ContactID']] = 'Skipped - Contact email not set.';

                            $this->skippedCounter = $this->skippedCounter + 1;

                            $markContactObj = $model::findByContactID($vendor['vendor']['ContactID']);

                            if ($markContactObj) {
                                $markContact = $markContactObj->toArray();

                                $markContact['baz_vendor_id'] = 0;
                                $markContact['baz_customer_id'] = 0;

                                $markContactObj->update($markContact);
                            }
                        }
                    }
                }
            }
        }
    }
}