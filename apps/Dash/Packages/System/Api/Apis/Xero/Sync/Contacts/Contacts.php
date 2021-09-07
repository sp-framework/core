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
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\History;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $syncDataDirectory = 'apps/Dash/Packages/System/Api/Apis/Xero/Sync/Contacts/SyncData/';

    protected $scheduleChildrenTasks = false;

    protected $vendorsPackage;

    protected $contactsPackage;

    protected $customersPackage;

    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    protected $addCounter = 0;

    protected $addedIds = [];

    protected $updateCounter = 0;

    protected $downloadCounter = 0;

    protected $downloadIds = [];

    protected $updatedIds = [];

    protected $errors = [];

    protected $errorIds = [];

    protected $responseData = [];

    protected $responseMessage = '';

    public function sync($apiId = null, $parameters = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetContactsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis && count($xeroApis) > 0) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id'], $parameters);
                }
            } else {
                $this->syncWithXero($apiId, $parameters);
            }

            $this->responseData = array_merge($this->responseData,
                [
                    'downloadIds' => $this->downloadIds
                ]
            );

            $this->responseMessage =
                $this->responseMessage . ' ' . 'Contacts Sync Ok. Downloaded: ' . $this->downloadCounter . '.';

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

        if ($parameters && isset($parameters[$apiId]['Contacts']['modifiedSince'])) {
            $modifiedSince = $parameters[$apiId]['Contacts']['modifiedSince'];//Should be set to UTC
        } else {
            $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetContacts', $apiId);
        }

        if ($modifiedSince) {
            // $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        $page = 1;

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

            $page++;
        } while (isset($responseArr['Contacts']) && count($responseArr['Contacts']) > 0);
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

                    if (!$this->apiPackage) {
                        $this->apiPackage = new Api;
                    }

                    if (!$this->api) {
                        $this->api = $this->apiPackage->useApi(['api_id' => $contact['api_id']]);
                    }

                    if (!$this->xeroApi) {
                        $this->xeroApi = $this->api->useService('XeroAccountingApi');
                    }

                    if ($this->addUpdateXeroContacts($contact['api_id'], $contact)) {
                        try {
                            $this->localContent->delete($files[$fileCount]);
                        } catch (FilesystemException | UnableToDeleteFile $exception) {
                            throw $exception;
                        }
                    }
                } catch (FilesystemException | UnableToReadFile $exception) {
                    throw $exception;
                }
            }

            $this->syncWithLocal();

            $this->responseData = array_merge($this->responseData,
                [
                    'addedIds' => $this->addedIds,
                    'updatedIds' => $this->updatedIds
                ]
            );

            $this->responseMessage =
                $this->responseMessage . ' ' . 'Contacts Sync Ok. Added: ' . $this->addCounter . '. Updated: ' . $this->updateCounter . '.';

            $this->addResponse(
                $this->responseMessage,
                0,
                $this->responseData
            );

            return $this->scheduleChildrenTasks;
        }
    }

    protected function addUpdateXeroContacts($apiId, $contact)
    {
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

            array_push($this->addedIds, $contact['ContactID']);

            $thisContact = $modelToUse->toArray();
        } else {
            if ($xeroContact->baz_vendor_id) {
                $contact['resync_local'] = '1';
            }

            $xeroContact->assign($this->jsonData($contact));

            $xeroContact->update();

            $this->updateCounter = $this->updateCounter + 1;

            array_push($this->updatedIds, $contact['ContactID']);

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
        $model = SystemApiXeroContacts::class;

        $xeroContact = $model::find(
            [
                'conditions'    => 'baz_vendor_id IS NULL OR resync_local = :rl:',
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

            if ($contacts && count($contacts) > 0) {
                foreach ($contacts as $contactKey => $contact) {
                    if ($contact['IsCustomer'] == '0' && $contact['IsSupplier'] == '0') {
                        continue;//We dont want to add just placeholder accounts.
                    }

                    $this->errors = [];
                    $this->errors['customers'] = [];
                    $this->errors['vendors'] = [];
                    $this->errors['address'] = [];

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
                        $this->generateCustomerData($contact);

                        if (count($this->errors['customers']) > 0) {
                            if (count($this->errors['address']) > 0) {
                                $errors = array_merge($this->errors['customers'], $this->errors['address']);
                            } else {
                                $errors = $this->errors['customers'];
                            }
                            $this->customersPackage->errorCustomer('Errors in customers. Please check details for more information.', Json::encode($errors));
                        }
                    } else if (($contact['IsCustomer'] == '0' && $contact['IsSupplier'] == '1') ||
                               ($contact['IsCustomer'] == '1' && $contact['IsSupplier'] == '1')
                    ) {
                        $this->generateVendorData($contact);

                        if (count($this->errors['vendors']) > 0) {
                            if (count($this->errors['address']) > 0) {
                                $errors = array_merge($this->errors['vendors'], $this->errors['address']);
                            } else {
                                $errors = $this->errors['vendors'];
                            }
                            $this->vendorsPackage->errorVendor('Errors in vendors. Please check details for more information.', Json::encode($errors));
                        }
                    }
                }
            }
        }
    }

    protected function generateCustomerData(array $contact)
    {
        $customer = [];

        if (isset($contact['EmailAddress']) && $contact['EmailAddress'] !== '') {
            $customer['account_email'] = $contact['EmailAddress'];
        } else {
            $this->errors['customers'] = array_merge($this->errors['customers'], ['Could not add customer data for Contact - ' . $contact['ContactID']]);

            return;
        }

        $customer['first_name'] = $contact['Name'];
        $customer['last_name'] = $contact['Name'];
        $customer['full_name'] = $contact['Name'];
        $customer['contact_mobile'] = '0';

        $customer['customer_group_id'] = '0';

        if ($contact['ContactGroups'] && $contact['ContactGroups'] !== '') {
            $contact['ContactGroups'] = Json::decode($contact['ContactGroups'], true);

            $model = SystemApiXeroContactGroups::class;

            foreach ($contact['ContactGroups'] as $groupKey => $group) {
                if ($group['Status'] === 'ACTIVE') {
                    $xeroContactGroup = $model::findFirst(
                        [
                            'conditions'    => 'ContactGroupID = :cgid:',
                            'bind'          =>
                                [
                                    'cgid'  => $group['ContactGroupID']
                                ]
                        ]
                    );

                    if ($xeroContactGroup) {
                        if ($xeroContactGroup->baz_customer_group_id) {
                            $customer['customer_group_id'] = $xeroContactGroup->baz_customer_group_id;
                            break;
                        }
                    }
                }
            }
        }

        $geo = $this->getAddressIds($contact);

        if (isset($geo['currency'])) {
            $customer['currency'] = $geo['currency'];
        }

        $customer['address_ids'] = Json::encode($geo['address_ids']);

        $customer = $this->customersPackage->updateAddresses($customer);

        if ($contact['phones'] && count($contact['phones']) > 0) {
            $customer['contact_other'] = '';

            foreach ($contact['phones'] as $phoneKey => $phone) {
                $phoneStr = '';

                if ($phone['PhoneCountryCode']) {
                    $phoneStr .= $phone['PhoneCountryCode'] . '-';
                }
                if ($phone['PhoneAreaCode']) {
                    $phoneStr .= $phone['PhoneAreaCode'] . '-';
                }
                if ($phone['PhoneNumber']) {
                    $phoneStr .= $phone['PhoneNumber'];
                }

                if ($phone['PhoneType'] === 'DEFAULT') {
                    $customer['contact_phone'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'DDI') {
                    if ($phoneStr !== '') {
                        $customer['contact_other'] .= 'Direct: ' . $phoneStr . ' ';
                    }
                } else if ($phone['PhoneType'] === 'FAX') {
                    $customer['contact_fax'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'MOBILE') {
                    if ($phoneStr !== '') {
                        $customer['contact_other'] .= 'Mobile: ' . $phoneStr . ' ';
                    }
                }
            }

            $customer['contact_other'] = trim($customer['contact_other']);
        }

        if (!isset($customer['contact_phone']) ||$customer['contact_phone'] === '') {
            $customer['contact_phone'] = '0';
        }

        $checkCustomer = $this->customersPackage->checkCustomerDuplicate($customer['account_email']);

        if ($contact['baz_customer_id'] && $contact['baz_customer_id'] != '0' || $checkCustomer) {
            if ($checkCustomer) {
                $customer['id'] = $checkCustomer->toArray()['id'];
            } else {
                $customer['id'] = $contact['baz_customer_id'];
            }

            if ($this->customersPackage->update($customer)) {
                $customer = $this->customersPackage->packagesData->last;

                $customerFinancials = $this->generateCustomerFinancialsData($contact, $customer);

                $this->customersPackage->updateFinancialDetails($customerFinancials);
            } else {

                $this->errors['customers'] = array_merge($this->errors['customers'], ['Could not update customer data - ' . $contact['Name']]);
            }
        } else {
            if ($this->customersPackage->add($customer)) {
                $customer = $this->customersPackage->packagesData->last;

                $customerFinancials = $this->generateCustomerFinancialsData($contact, $customer);

                $this->customersPackage->addFinancialDetails($customerFinancials);
            } else {

                $this->errors['customers'] = array_merge($this->errors['customers'], ['Could not add customer data - ' . $contact['Name']]);
            }
        }

        if ($customer['contact_phone'] === '0') {
            $this->errors['customers'] = array_merge($this->errors['customers'], ['Phone missing for customer - ' . $contact['Name']]);
        }

        if ($contact['HasAttachments'] == '1') {
            $this->addContactAttachments($contact, $customer);
        }

        $this->addContactHistory($contact, $customer);

        $contact['baz_customer_id'] = $customer['id'];
        $contact['resync_local'] = null;

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

        $xeroContact->assign($this->jsonData($contact));

        $xeroContact->update();
    }

    protected function generateVendorData(array $contact)
    {
        $vendor = [];

        if ($contact['IsCustomer'] == '1') {
            $vendor['is_b2b_customer'] = '1';
        }

        if ($contact['finance']['TaxNumber']) {
            $vendor['abn'] = str_replace(' ', '', $contact['finance']['TaxNumber']);
        } else {
            $vendor['abn'] = '00000000000';
        }

        $vendor['business_name'] = $contact['Name'];

        $vendor['vendor_group_id'] = '0';

        if ($contact['ContactGroups'] && $contact['ContactGroups'] !== '') {
            $contact['ContactGroups'] = Json::decode($contact['ContactGroups'], true);

            $model = SystemApiXeroContactGroups::class;

            foreach ($contact['ContactGroups'] as $groupKey => $group) {
                if ($group['Status'] === 'ACTIVE') {
                    $xeroContactGroup = $model::findFirst(
                        [
                            'conditions'    => 'ContactGroupID = :cgid:',
                            'bind'          =>
                                [
                                    'cgid'  => $group['ContactGroupID']
                                ]
                        ]
                    );

                    if ($xeroContactGroup) {
                        if ($xeroContactGroup->baz_vendor_group_id) {
                            $vendor['vendor_group_id'] = $xeroContactGroup->baz_vendor_group_id;
                            break;
                        }
                    }
                }
            }
        }

        $vendor['is_supplier'] = '1';

        $geo = $this->getAddressIds($contact);

        if (isset($geo['currency'])) {
            $vendor['currency'] = $geo['currency'];
        }

        $vendor['address_ids'] = Json::encode($geo['address_ids']);

        $vendor = $this->vendorsPackage->updateAddresses($vendor);

        if ($contact['EmailAddress']) {
            $vendor['email'] = $contact['EmailAddress'];
        } else {
            $vendor['email'] = 'missing';
        }

        $vendor['website'] = $contact['Website'];

        if ($contact['phones'] && count($contact['phones']) > 0) {
            $vendor['contact_other'] = '';

            foreach ($contact['phones'] as $phoneKey => $phone) {
                $phoneStr = '';

                if ($phone['PhoneCountryCode']) {
                    $phoneStr .= $phone['PhoneCountryCode'] . '-';
                }
                if ($phone['PhoneAreaCode']) {
                    $phoneStr .= $phone['PhoneAreaCode'] . '-';
                }
                if ($phone['PhoneNumber']) {
                    $phoneStr .= $phone['PhoneNumber'];
                }

                if ($phone['PhoneType'] === 'DEFAULT') {
                    $vendor['contact_phone'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'DDI') {
                    if ($phoneStr !== '') {
                        $vendor['contact_other'] .= 'Direct: ' . $phoneStr . ' ';
                    }
                } else if ($phone['PhoneType'] === 'FAX') {
                    $vendor['contact_fax'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'MOBILE') {
                    if ($phoneStr !== '') {
                        $vendor['contact_other'] .= 'Mobile: ' . $phoneStr . ' ';
                    }
                }
            }

            $vendor['contact_other'] = trim($vendor['contact_other']);
        }

        if (!isset($vendor['contact_phone']) ||$vendor['contact_phone'] === '') {
            $vendor['contact_phone'] = '0';
        }

        $checkVendor = $this->vendorsPackage->checkVendorDuplicate($vendor['business_name']);

        if ($contact['baz_vendor_id'] && $contact['baz_vendor_id'] != '0' || $checkVendor) {
            if ($checkVendor) {
                $vendor['id'] = $checkVendor->toArray()['id'];
            } else {
                $vendor['id'] = $contact['baz_vendor_id'];
            }

            if ($this->vendorsPackage->update($vendor)) {
                $vendor = $this->vendorsPackage->packagesData->last;

                $vendorFinancials = $this->generateVendorFinancialsData($contact, $vendor);

                $this->vendorsPackage->updateFinancialDetails($vendorFinancials);
            } else {

                $this->errors['vendors'] = array_merge($this->errors['vendors'], ['Could not update vendor data - ' . $vendor['business_name']]);
            }
        } else {
            if ($this->vendorsPackage->add($vendor)) {
                $vendor = $this->vendorsPackage->packagesData->last;

                $vendorFinancials = $this->generateVendorFinancialsData($contact, $vendor);

                $this->vendorsPackage->addFinancialDetails($vendorFinancials);
            } else {

                $this->errors['vendors'] = array_merge($this->errors['vendors'], ['Could not add vendor data - ' . $vendor['business_name']]);
            }
        }

        if ($vendor['email'] === 'missing') {
            $this->errors['vendors'] = array_merge($this->errors['vendors'], ['Email missing for vendor - ' . $vendor['business_name']]);
        }
        if ($vendor['abn'] === '00000000000') {
            $this->errors['vendors'] = array_merge($this->errors['vendors'], ['ABN missing for vendor - ' . $vendor['business_name']]);
        }
        if ($vendor['contact_phone'] === '0') {
            $this->errors['vendors'] = array_merge($this->errors['vendors'], ['Phone missing for vendor - ' . $vendor['business_name']]);
        }

        if ($contact['persons'] && count($contact['persons']) > 0) {
            $this->generateVendorContacts($contact, $vendor['id']);
        }

        if ($contact['HasAttachments'] == '1') {
            $this->addContactAttachments($contact, $vendor);
        }

        $this->addContactHistory($contact, $vendor);

        $contact['baz_vendor_id'] = $vendor['id'];
        $contact['resync_local'] = null;

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

        $xeroContact->assign($this->jsonData($contact));

        $xeroContact->update();
    }

    protected function getAddressIds(array $contact)
    {
        $geo['currency'] = '0';
        $geo['address_ids'] = [];
        $geo['address_ids']['1'] = [];
        $geo['address_ids']['2'] = [];

        if (count($contact['addresses']) > 0) {
            foreach ($contact['addresses'] as $addressKey => $address) {
                if (!$address['City'] || $address['City'] === '' ||
                    !$address['Region'] || $address['Region'] === '' ||
                    !$address['Country'] || $address['Country'] === ''
                ) {
                    continue;
                }

                $found = false;

                //Xero Uses an address API for address verification, so the address received from Xero is accurate.
                //If we do not have matching data in our system, we create new GeoLocation data.
                if ($this->basepackages->geoCities->searchCities($address['City'])) {
                    $cityData = $this->basepackages->geoCities->packagesData->cities;

                    if (count($cityData) > 0) {
                        foreach ($cityData as $cityKey => $city) {
                            if (strtolower($city['name']) === strtolower($address['City'])) {
                                $found = true;

                                $newAddress['city_id'] = $city['id'];
                                $newAddress['city_name'] = $city['name'];
                                $newAddress['state_id'] = $city['state_id'];
                                $newAddress['state_name'] = $city['state_name'];
                                $newAddress['country_id'] = $city['country_id'];
                                $newAddress['country_name'] = $city['country_name'];
                            }

                            if ($found) {
                                break;
                            }
                        }
                    }
                }

                if (!$found) {
                    //Country
                    $foundCountry = null;

                    if ($this->basepackages->geoCountries->searchCountries($address['Country'], true)) {
                        $countryData = $this->basepackages->geoCountries->packagesData->countries;

                        if (count($countryData) > 0) {
                            foreach ($countryData as $countryKey => $country) {
                                if (strtolower($country['name']) === strtolower($address['Country'])) {
                                    $foundCountry = $country;
                                    $geo['currency'] = $country['currency'];
                                    break;
                                }
                            }
                        }
                    }

                    if (!$foundCountry) {
                        $newCountry['name'] = $address['Country'];
                        $newCountry['installed'] = '1';
                        $newCountry['enabled'] = '1';
                        $newCountry['user_added'] = '1';

                        if ($this->basepackages->geoCountries->add($newCountry)) {
                            $newAddress['country_id'] = $this->basepackages->geoCountries->packagesData->last['id'];
                            $newAddress['country_name'] = $newCountry['name'];
                        } else {

                            $this->errors['address'] = array_merge($this->errors['address'], ['Could not add country data.']);

                            continue;
                        }
                    } else {
                        //We check if country is installed or not, if not, we install and enable it
                        if ($foundCountry['installed'] != '1') {
                            $foundCountry['enabled'] = '1';

                            $this->basepackages->geoCountries->installCountry($foundCountry);
                        } else if ($foundCountry['enabled'] != '1') {
                            $foundCountry['enabled'] = '1';

                            $this->basepackages->geoCountries->update($foundCountry);
                        }

                        $newAddress['country_id'] = $foundCountry['id'];
                        $newAddress['country_name'] = $foundCountry['name'];
                    }

                    //State (Region in Xero Address)
                    $foundState = null;

                    if ($this->basepackages->geoStates->searchStatesByCode($address['Region'], true)) {
                        $stateData = $this->basepackages->geoStates->packagesData->states;

                        if (count($stateData) > 0) {
                            foreach ($stateData as $stateKey => $state) {
                                if (strtolower($state['state_code']) === strtolower($address['Region'])) {
                                    $foundState = $state;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$foundState) {
                        $newState['name'] = $address['Region'];
                        $newState['state_code'] = substr($address['Region'], 0, 3);
                        $newState['user_added'] = '1';
                        $newState['country_id'] = $newAddress['country_id'];

                        if ($this->basepackages->geoStates->add($newState)) {
                            $newAddress['state_id'] = $this->basepackages->geoStates->packagesData->last['id'];
                            $newAddress['state_name'] = $newState['name'];
                        } else {

                            $this->errors['address'] = array_merge($this->errors['address'], ['Could not add state data.']);

                            continue;
                        }
                    } else {
                        $newAddress['state_id'] = $foundState['id'];
                        $newAddress['state_name'] = $foundState['name'];
                    }

                    //New City
                    $newCity['name'] = $address['City'];
                    $newCity['state_id'] = $newAddress['state_id'];
                    $newCity['country_id'] = $newAddress['country_id'];
                    $newCity['user_added'] = '1';

                    if ($this->basepackages->geoCities->add($newCity)) {
                        $newAddress['city_id'] = $this->basepackages->geoCities->packagesData->last['id'];
                        $newAddress['city_name'] = $newCity['name'];
                    } else {

                        $this->errors['address'] = array_merge($this->errors['address'], ['Could not add city data.']);

                        continue;
                    }
                }

                $newAddress['seq'] = 0;
                $newAddress['new'] = 1;
                $newAddress['attention_to'] = $address['AttentionTo'];
                $newAddress['street_address'] = $address['AddressLine1'];
                $newAddress['street_address_2'] = $address['AddressLine2'];

                if ($address['AddressType'] === 'POBOX') {
                    array_push($geo['address_ids']['2'], $newAddress);
                } else if ($address['AddressType'] === 'DELIVERY' ||
                           $address['AddressType'] === 'STREET'
                ) {
                    array_push($geo['address_ids']['1'], $newAddress);
                }
            }
        }

        return $geo;
    }

    protected function generateVendorFinancialsData($contact, $vendor)
    {
        $vendorFinancials['vendor_id'] = $vendor['id'];
        $vendorFinancials['acn'] = substr($vendor['abn'], 2);

        if ($contact['finance']['DefaultCurrency']) {
            $vendorFinancials['currency'] = $contact['finance']['DefaultCurrency'];
        } else if (isset($vendor['currency'])) {
            $vendorFinancials['currency'] = $vendor['currency'];
        } else {
            $vendorFinancials['currency'] = '0';
        }

        if ($contact['finance']['BankAccountDetails']) {
            $vendorFinancials['account_number'] = $contact['finance']['BankAccountDetails'];
        }

        if ($contact['finance']['PaymentTermsBillsDay']) {
            $vendorFinancials['bills_due_date'] = $contact['finance']['PaymentTermsBillsDay'];
        }
        if ($contact['finance']['PaymentTermsBillsType']) {
            $vendorFinancials['bills_due_date_term'] = $contact['finance']['PaymentTermsBillsType'];
        }

        if ($contact['finance']['Discount']) {
            $vendorFinancials['bills_discount'] = $contact['finance']['Discount'];
        }

        return $vendorFinancials;
    }

    protected function generateCustomerFinancialsData($contact, $customer)
    {
        $customerFinancials['customer_id'] = $customer['id'];

        if ($contact['finance']['TaxNumber']) {
            $customerFinancials['abn'] = str_replace(' ', '', $contact['finance']['TaxNumber']);
        } else {
            $customerFinancials['abn'] = '00000000000';
        }

        if ($contact['finance']['DefaultCurrency']) {
            $customerFinancials['currency'] = $contact['finance']['DefaultCurrency'];
        } else if (isset($customer['currency'])) {
            $customerFinancials['currency'] = $customer['currency'];
        } else {
            $customerFinancials['currency'] = '0';
        }

        if ($contact['finance']['BankAccountDetails']) {
            $customerFinancials['account_number'] = $contact['finance']['BankAccountDetails'];
        }

        if ($contact['finance']['PaymentTermsSalesDay']) {
            $customerFinancials['invoices_due_date'] = $contact['finance']['PaymentTermsSalesDay'];
        }
        if ($contact['finance']['PaymentTermsSalesType']) {
            $customerFinancials['invoices_due_date_term'] = $contact['finance']['PaymentTermsSalesType'];
        }

        if ($contact['finance']['Discount']) {
            $customerFinancials['invoices_discount'] = $contact['finance']['Discount'];
        }

        return $customerFinancials;
    }

    protected function generateVendorContacts($contact, $vendorId)
    {
        foreach ($contact['persons'] as $personKey => $person) {
            if (isset($person['EmailAddress'])) {
                $newContact = [];
                $newContact['account_email'] = $person['EmailAddress'];
                $newContact['vendor_id'] = $vendorId;
                $newContact['first_name'] = $person['FirstName'];
                $newContact['last_name'] = $person['LastName'];
                $newContact['full_name'] = $newContact['first_name'] . ' ' . $newContact['last_name'];
                $newContact['contact_phone'] = '0';
                $newContact['contact_mobile'] = '0';

                $checkContact = $this->contactsPackage->checkContactDuplicate($newContact['account_email']);
                if ($person['baz_contact_id'] && $person['baz_contact_id'] != '0' || $checkContact) {
                    if ($checkContact) {
                        $newContact['id'] = $checkContact->toArray()['id'];
                    } else {
                        $newContact['id'] = $person['baz_contact_id'];
                    }

                    if (!$this->contactsPackage->update($newContact)) {
                        $person['baz_contact_id'] = $this->contactsPackage->packagesData->last['id'];

                        $this->errors['address'] = array_merge($this->errors['address'], ['Could not update contact data.']);
                    }
                } else {
                    if ($this->contactsPackage->add($newContact)) {
                        $person['baz_contact_id'] = $this->contactsPackage->packagesData->last['id'];
                        $this->contactsPackage->errorContact('Phone and Mobile missing for contact - ' . $newContact['account_email']);
                    } else {

                        $this->errors['address'] = array_merge($this->errors['address'], ['Could not add contact data.']);
                    }
                }

                if (isset($person['baz_contact_id'])) {
                    $model = SystemApiXeroContactsContactPersons::class;

                    $xeroPerson = $model::findFirst(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );

                    $xeroPerson->assign($person);

                    $xeroPerson->update();
                }
            }
        }
    }

    protected function addContactAttachments($contact, $vendor)
    {
        $model = SystemApiXeroAttachments::class;

        $xeroAttachment = $model::find(
            [
                'conditions'    => 'baz_storage_local_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'contacts',
                        'xpri'  => $contact['ContactID']
                    ]
            ]
        );

        if ($xeroAttachment) {
            $attachments = $xeroAttachment->toArray();

            if (count($attachments) > 0) {

                $request = new GetContactAttachmentByIdRestRequest;

                foreach ($attachments as $attachmentKey => $attachment) {
                    $request->ContactID = $attachment['xero_package_row_id'];

                    $request->AttachmentID = $attachment['AttachmentID'];

                    $response = $this->xeroApi->getContactAttachmentById($request);

                    if ($response) {
                        $this->api->refreshXeroCallStats($response->getHeaders());

                        if ($response->getStatusCode() === 200) {
                            $storageId = $this->addAttachmentToStorage($attachment, $contact, $vendor, $response);

                            if ($storageId) {
                                $xA = $model::findFirst(
                                    [
                                        'conditions'    => 'AttachmentID = :aid:',
                                        'bind'          =>
                                            [
                                                'aid'   => $attachment['AttachmentID']
                                            ]
                                    ]
                                );

                                if ($xA) {
                                    $xA->baz_storage_local_id = $storageId['id'];

                                    $xA->update();

                                    $this->basepackages->storages->changeOrphanStatus($storageId['uuid'], null, false, 0);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function addAttachmentToStorage($attachment, $contact, $vendor, $response)
    {
        if ($this->basepackages->storages->storeFile(
                'private',
                'contacts',
                $response->getBody()->getContents(),
                $attachment['FileName'],
                $attachment['ContentLength'],
                $attachment['MimeType'],
            )
        ) {
            $newNote['package_row_id'] = $vendor['id'];
            $newNote['note_type'] = '1';
            $newNote['note_app_visibility']['data'] = [];
            $newNote['is_private'] = '0';
            $newNote['note'] = 'Added via Xero API.';
            $newNote['note_attachments'][] = $this->basepackages->storages->packagesData->storageData['uuid'];

            $this->basepackages->notes->addNote('vendors', $newNote);

            return $this->basepackages->storages->packagesData->storageData;
        }

        return false;
    }

    protected function addContactHistory($contact, $vendor)
    {
        $model = SystemApiXeroHistory::class;

        $xeroHistory = $model::find(
            [
                'conditions'    => 'baz_note_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'contacts',
                        'xpri'  => $contact['ContactID']
                    ]
            ]
        );

        if ($xeroHistory) {
            $histories = $xeroHistory->toArray();

            if (count($histories) > 0) {
                foreach ($histories as $historyKey => $history) {

                    $note = $this->addHistoryToNote($history, $vendor);

                    if ($note) {
                        $xH = $model::findFirstById($history['id']);

                        if ($xH) {
                            $xH->baz_note_id = $note['id'];

                            $xH->update();
                        }
                    }
                }
            }
        }
    }

    protected function addHistoryToNote($history, $vendor)
    {
        $newNote['package_row_id'] = $vendor['id'];
        $newNote['note_type'] = '1';
        $newNote['note_app_visibility']['data'] = [];
        $newNote['is_private'] = '0';
        $newNote['note'] =
            'Added via Xero API.' .
            '<br>Change Type: ' . $history['Changes'] .
            '<br>Created At: ' . \DateTime::createFromFormat('Y-m-d\TH:i:s', $history['DateUTCString'])->format('Y-m-d H:i:s') .
            '<br>Details: ' . $history['Details'];

        $this->basepackages->notes->addNote('vendors', $newNote);

        if ($this->basepackages->notes->packagesData->last) {
            return $this->basepackages->notes->packagesData->last;
        }

        return false;
    }
}