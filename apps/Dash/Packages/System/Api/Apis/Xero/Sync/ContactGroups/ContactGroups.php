<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $apiPackage;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetContactsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if (!$apiId) {
            foreach ($xeroApis as $key => $xeroApi) {
                $this->syncWithXero($xeroApi['api_id']);
            }
        } else {
            $this->syncWithXero($apiId);
        }
        die();
    }

    protected function syncWithXero($apiId)
    {
        $api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $xeroAccountingApi = $api->useService('XeroAccountingApi');

        // $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetContacts', $apiId);

        // if ($modifiedSince) {
        //     $xeroAccountingApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        // }

        $page = 1;

        do {
            $this->request->page = $page;

            $this->request->includeArchived = true;

            $response = $xeroAccountingApi->getContacts($this->request);

            $api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ($responseArr['Status'] === 'OK' && isset($responseArr['Contacts'])) {
                if (count($responseArr['Contacts']) > 0) {
                    // $this->addUpdateXeroContacts($responseArr['Contacts']);
                }
            }

            var_dump($responseArr);
            $page++;
        } while (isset($responseArr['Contacts']) && count($responseArr['Contacts']) > 0);
    }

    protected function addUpdateXeroContacts($contacts)
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

            $modelToUse = new $model();

            $modelToUse->assign($contact);

            if ($xeroContact->count() === 0) {
                $modelToUse->create();
            } else {
                if ($contact['UpdatedDateUTC'] !== $xeroContact->UpdatedDateUTC) {
                    $modelToUse->update();
                }
            }

            if (isset($contact['Phones']) && count($contact['Phones']) > 0) {
                $this->addUpdateXeroContactsPhones($contact);
            }
        }
    }

    protected function addUpdateXeroContactsPhones($contact)
    {
        $model = SystemApiXeroContactsPhones::class;

        foreach ($contact['Phones'] as $phoneKey => $phone) {
            $xeroContact = $model::findFirst(
                [
                    'conditions'    => 'PurchaseOrderID = :poid: AND ContactID = :cid:',
                    'bind'          =>
                        [
                            'poid'  => $contact['PurchaseOrderID'],
                            'cid'   => $contact['ContactID']
                        ]
                ]
            );

            $modelToUse = new $model();

            $contact['PurchaseOrderID'] = $contact['PurchaseOrderID'];

            $modelToUse->assign($contact);

            if ($xeroContact->count() === 0) {
                $modelToUse->create();
            } else {
                $modelToUse->update();
            }
        }
    }
}