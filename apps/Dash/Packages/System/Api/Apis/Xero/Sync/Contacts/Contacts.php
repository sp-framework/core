<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Attachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsAddresses;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsContactPersons;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsFinance;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\History;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId = null, $parameters = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetContactsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if (!$apiId) {
            foreach ($xeroApis as $key => $xeroApi) {
                $this->syncWithXero($xeroApi['api_id'], $parameters);
            }
        } else {
            $this->syncWithXero($apiId, $parameters);
        }
    }

    protected function syncWithXero($apiId, $parameters = null)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        if ($parameters && isset($parameters[$apiId]['Contacts']['modifiedSince'])) {
            $modifiedSince = $parameters[$apiId]['Contacts']['modifiedSince'];
        } else {
            $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetContacts', $apiId);
        }

        if ($modifiedSince) {
            $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        $page = 1;

        do {
            $this->request->page = $page;

            $this->request->includeArchived = true;

            $response = $this->xeroApi->getContacts($this->request);

            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
                isset($responseArr['Contacts'])
            ) {
                if (count($responseArr['Contacts']) > 0) {
                    $this->addUpdateXeroContacts($apiId, $responseArr['Contacts']);
                }
            }

            $page++;
        } while (isset($responseArr['Contacts']) && count($responseArr['Contacts']) > 0);
    }

    protected function addUpdateXeroContacts($apiId, $contacts)
    {
        foreach ($contacts as $contactKey => $contact) {
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

                $thisContact = $modelToUse->toArray();
            } else {
                if ($contact['UpdatedDateUTC'] !== $xeroContact->UpdatedDateUTC) {

                    $xeroContact->assign($this->jsonData($contact));

                    $xeroContact->update();

                    $thisContact = $xeroContact->toArray();
                } else {
                    continue;
                }
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
        }
    }

    protected function getContactsAttachments($contactId)
    {
        $request = new GetContactAttachmentsRestRequest;

        $request->ContactID = $contactId;

        $response = $this->xeroApi->getContactAttachments($request);

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
}