<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups;

use Apps\Dash\Packages\Business\Directory\VendorGroups\VendorGroups;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestRequest;
use System\Base\BasePackage;

class ContactGroups extends BasePackage
{
    protected $vendorGroupsPackage;

    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    protected $addCounter = 0;

    protected $addedIds = [];

    protected $updateCounter = 0;

    protected $updatedIds = [];

    protected $responseData = [];

    protected $responseMessage = '';

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetContactGroupsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis && count($xeroApis) > 0) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id']);
                }
            } else {
                $this->syncWithXero($apiId);
            }

            $this->syncWithLocal();

            $this->responseData = array_merge($this->responseData,
                [
                    'addedIds' => $this->addedIds,
                    'updatedIds' => $this->updatedIds
                ]
            );

            $this->responseMessage =
                $this->responseMessage . ' ' . 'Contact Groups Sync Ok. Added: ' . $this->addCounter . '. Updated: ' . $this->updateCounter . '.';

            $this->addResponse(
                $this->responseMessage,
                0,
                $this->responseData
            );
        } else {
            $this->addResponse('Sync Error. No API Configuration Found', 1);
        }
    }

    protected function syncWithXero($apiId)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        $response = $this->xeroApi->getContactGroups($this->request);

        $this->api->refreshXeroCallStats($response->getHeaders());

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

                $this->addCounter = $this->addCounter + 1;

                array_push($this->addedIds, $contactGroup['ContactGroupID']);
            } else {
                if ($xeroContactGroup->baz_vendor_group_id) {
                    $contactGroup['resync_local'] = '1';
                }

                $xeroContactGroup->assign($contactGroup);

                $xeroContactGroup->update();

                $this->updateCounter = $this->updateCounter + 1;

                array_push($this->updatedIds, $contactGroup['ContactGroupID']);
            }
        }
    }

    public function syncWithLocal()
    {
        $model = SystemApiXeroContactGroups::class;

        $xeroVg = $model::find(
            [
                'conditions'    => 'baz_vendor_group_id IS NULL OR resync_local = :rl:',
                'bind'          =>
                    [
                        'rl'    => '1',
                    ]
            ]
        );

        if ($xeroVg) {
            $this->vendorGroupsPackage = $this->usePackage(VendorGroups::class);

            $vgs = $xeroVg->toArray();

            if ($vgs && count($vgs) > 0) {
                foreach ($vgs as $vgKey => $vg) {
                    $this->generateVgData($vg, $model);
                }
            }
        }
    }

    protected function generateVgData(array $vg, $model)
    {
        $vendorGroup['name'] = $vg['Name'];
        $vendorGroup['description'] = 'Added via Xero API.';

        if (!$vg['baz_vendor_group_id'] || $vg['resync_local'] == '1') {
            $xeroContactGroup = $model::findFirst(
                [
                    'conditions'    => 'ContactGroupID = :cgid:',
                    'bind'          =>
                        [
                            'cgid'  => $vg['ContactGroupID']
                        ]
                ]
            );

            if (!$vg['baz_vendor_group_id']) {
                if ($this->vendorGroupsPackage->add($vendorGroup)) {
                    $vg['baz_vendor_group_id'] = $this->vendorGroupsPackage->packagesData->last['id'];
                }
            } else {
                $vendorGroup['id'] = $vg['baz_vendor_group_id'];

                $this->vendorGroupsPackage->update($vendorGroup);
            }

            $vg['resync_local'] = null;

            $xeroContactGroup->assign($vg);

            $xeroContactGroup->update();
        }
    }
}