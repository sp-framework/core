<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups;

use Apps\Dash\Packages\Business\Directory\VendorGroups\VendorGroups;
use Apps\Dash\Packages\Crms\CustomerGroups\CustomerGroups;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestRequest;
use System\Base\BasePackage;

class ContactGroups extends BasePackage
{
    protected $vendorGroupsPackage;

    protected $customerGroupsPackage;

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
                if ($xeroContactGroup->baz_vendor_group_id || $xeroContactGroup->baz_customer_group_id) {
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

        $xeroGroups = $model::find(
            [
                'conditions'    => 'baz_vendor_group_id IS NULL OR baz_customer_group_id IS NULL OR resync_local = :rl:',
                'bind'          =>
                    [
                        'rl'    => '1',
                    ]
            ]
        );

        if ($xeroGroups) {
            $this->vendorGroupsPackage = $this->usePackage(VendorGroups::class);

            $this->customerGroupsPackage = $this->usePackage(CustomerGroups::class);

            $groups = $xeroGroups->toArray();

            if ($groups && count($groups) > 0) {
                foreach ($groups as $groupKey => $group) {
                    $vendorGroup = $this->generateVendorGroupData($group);
                    $customerGroup = $this->generateCustomerGroupData($group);

                    if ($vendorGroup && $customerGroup) {
                        $xeroContactGroup = $model::findFirst(
                            [
                                'conditions'    => 'ContactGroupID = :cgid:',
                                'bind'          =>
                                    [
                                        'cgid'  => $group['ContactGroupID']
                                    ]
                            ]
                        );

                        $group['resync_local'] = null;
                        $group['baz_vendor_group_id'] = $vendorGroup['id'];
                        $group['baz_customer_group_id'] = $customerGroup['id'];

                        $group = array_merge($xeroContactGroup->toArray(), $group);

                        $xeroContactGroup->assign($group);

                        $xeroContactGroup->update();
                    }
                }
            }
        }
    }

    protected function generateVendorGroupData(array $group)
    {
        $vendorGroup['name'] = $group['Name'];
        $vendorGroup['description'] = 'Added via Xero API.';

        if (!$group['baz_vendor_group_id'] || $group['resync_local'] == '1') {
            if ($group['baz_vendor_group_id'] && $group['baz_vendor_group_id'] != '0') {
                if ($this->vendorGroupsPackage->getById($group['baz_vendor_group_id'])) {
                    $vendorGroup = array_merge($this->vendorGroupsPackage->getById($group['baz_vendor_group_id']), $vendorGroup);

                    if ($this->vendorGroupsPackage->update($vendorGroup)) {
                        $vendorGroup = $this->vendorGroupsPackage->packagesData->last;
                    } else {
                        $this->errors = array_merge($this->errors, ['Could not update vendor group data - ' . $vendorGroup['name']]);
                    }
                } else {
                    if ($this->vendorGroupsPackage->add($vendorGroup)) {
                        $vendorGroup = $this->vendorGroupsPackage->packagesData->last;
                    } else {
                        $this->errors = array_merge($this->errors, ['Could not add vendor group data - ' . $vendorGroup['name']]);
                    }
                }
            } else {
                if ($this->vendorGroupsPackage->add($vendorGroup)) {
                    $vendorGroup = $this->vendorGroupsPackage->packagesData->last;
                } else {
                    $this->errors = array_merge($this->errors, ['Could not add vendor group data - ' . $vendorGroup['name']]);
                }
            }

            return $vendorGroup;
        }

        return false;
    }

    protected function generateCustomerGroupData(array $group)
    {
        $customerGroup['name'] = $group['Name'];
        $customerGroup['description'] = 'Added via Xero API.';

        if (!$group['baz_customer_group_id'] || $group['resync_local'] == '1') {
            if ($group['baz_customer_group_id'] && $group['baz_customer_group_id'] != '0') {
                if ($this->customerGroupsPackage->getById($group['baz_customer_group_id'])) {
                    $customerGroup = array_merge($this->customerGroupsPackage->getById($group['baz_customer_group_id']), $customerGroup);

                    if ($this->customerGroupsPackage->update($customerGroup)) {
                        $customerGroup = $this->customerGroupsPackage->packagesData->last;
                    } else {
                        $this->errors = array_merge($this->errors, ['Could not update customer group data - ' . $customerGroup['name']]);
                    }
                } else {
                    if ($this->customerGroupsPackage->add($customerGroup)) {
                        $customerGroup = $this->customerGroupsPackage->packagesData->last;
                    } else {
                        $this->errors = array_merge($this->errors, ['Could not add customer group data - ' . $customerGroup['name']]);
                    }
                }
            } else {
                if ($this->customerGroupsPackage->add($customerGroup)) {
                    $customerGroup = $this->customerGroupsPackage->packagesData->last;
                } else {
                    $this->errors = array_merge($this->errors, ['Could not add customer group data - ' . $customerGroup['name']]);
                }
            }

            return $customerGroup;
        }

        return false;
    }
}