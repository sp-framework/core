<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Model\SystemApiXeroItems;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemsRestRequest;
use System\Base\BasePackage;

class Items extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetItemsRestRequest;

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
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        $response = $this->xeroApi->getItems($this->request);

        $responseArr = $response->toArray();

        $this->api->refreshXeroCallStats($response->getHeaders());

        if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
            isset($responseArr['Items'])
        ) {
            if (count($responseArr['Items']) > 0) {
                $this->addUpdateXeroItems($apiId, $responseArr['Items']);
            }
        }
    }

    protected function addUpdateXeroItems($apiId, $items)
    {
        foreach ($items as $itemKey => $item) {
            $model = SystemApiXeroItems::class;

            $xeroItem = $model::findFirst(
                [
                    'conditions'    => 'ItemID = :iid:',
                    'bind'          =>
                        [
                            'iid'   => $item['ItemID']
                        ]
                ]
            );

            $item['api_id'] = $apiId;

            if (!$xeroItem) {
                $modelToUse = new $model();

                $modelToUse->assign($this->jsonData($item));

                $modelToUse->create();

                $thisItem = $modelToUse->toArray();
            } else {
                if ($item['UpdatedDateUTC'] !== $xeroItem->UpdatedDateUTC) {

                    if ($xeroItem->baz_product_id) {
                        $item['resync_local'] = '1';
                    }

                    $xeroItem->assign($this->jsonData($item));

                    $xeroItem->update();

                    $thisItem = $xeroItem->toArray();
                } else {
                    continue;
                }
            }
        }
    }
}