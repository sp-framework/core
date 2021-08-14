<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestRequest;
use System\Base\BasePackage;

class ContactGroups extends BasePackage
{
    protected $apiPackage;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetContactGroupsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if (!$apiId) {
            foreach ($xeroApis as $key => $xeroApi) {
                $this->syncWithXero($xeroApi['api_id']);
            }
        } else {
            $this->syncWithXero($apiId);
        }
    }

    protected function syncWithXero($apiId)
    {
        $api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $xeroAccountingApi = $api->useService('XeroAccountingApi');

        $response = $xeroAccountingApi->getContactGroups($this->request);

        $api->refreshXeroCallStats($response->getHeaders());

        $responseArr = $response->toArray();

        if ($responseArr['Status'] === 'OK' && isset($responseArr['ContactGroups'])) {
            if (count($responseArr['ContactGroups']) > 0) {
                $this->addUpdateXeroContactGroups($apiId, $responseArr['ContactGroups']);
            }
        }
    }

    protected function addUpdateXeroContactGroups($apiId, $contactGroups)
    {
        foreach ($contactGroups as $contactGroupKey => $contactGroup) {
            $model = SystemApiXeroContactGroups::class;

            $xeroContactGroup = $model::findFirst(
                [
                    'conditions'    => 'ContactGroupID = :cgid:',
                    'bind'          =>
                        [
                            'cgid'  => $contactGroup['ContactGroupID']
                        ]
                ]
            );

            $contactGroup['api_id'] = $apiId;

            if (!$xeroContactGroup) {
                $modelToUse = new $model();

                $modelToUse->assign($contactGroup);

                $modelToUse->create();
            } else {
                $xeroContactGroup->assign($contactGroup);

                $xeroContactGroup->update();
            }
        }
    }
}