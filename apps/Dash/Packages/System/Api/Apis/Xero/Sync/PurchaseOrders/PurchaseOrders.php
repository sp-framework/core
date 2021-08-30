<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Attachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\ContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\History;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Items;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Organisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrders;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrdersLineitems;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest;
use System\Base\BasePackage;

class PurchaseOrders extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    protected $addCounter = 0;

    protected $addedIds = [];

    protected $updateCounter = 0;

    protected $updatedIds = [];

    public function sync($apiId = null, $parameters = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetPurchaseOrdersRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis && count($xeroApis) > 0) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id'], $parameters);
                }
            } else {
                $this->syncWithXero($apiId, $parameters);
            }

            $this->addResponse(
                'Sync Ok. Added: ' . $this->addCounter . '. Updated: ' . $this->updateCounter . '.',
                0,
                [
                    'addedIds' => $this->addedIds, 'updatedIds' => $this->updatedIds
                ]
            );
        } else {
            $this->addResponse('Sync Error. No API Configuration Found', 1);
        }
    }

    protected function syncWithXero($apiId, $parameters = null)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->syncDependencies($apiId, $parameters);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['modifiedSince'])) {
            $modifiedSince = $parameters[$apiId]['PurchaseOrders']['modifiedSince'];
        } else {
            $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetPurchaseOrders', $apiId);
        }
        if ($modifiedSince) {
            $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['dateFrom'])) {
            $this->request->DateFrom = $parameters[$apiId]['PurchaseOrders']['dateFrom'];
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['dateTo'])) {
            $this->request->DateTo = $parameters[$apiId]['PurchaseOrders']['dateTo'];
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['status'])) {
            $this->request->Status = $parameters[$apiId]['PurchaseOrders']['status'];
        }

        $page = 1;

        do {
            $this->request->page = $page;

            $response = $this->xeroApi->getPurchaseOrders($this->request);

            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
                isset($responseArr['PurchaseOrders'])
            ) {
                if (count($responseArr['PurchaseOrders']) > 0) {
                    $this->addUpdateXeroPurchaseOrders($apiId, $responseArr['PurchaseOrders']);
                }
            }

            $page++;
        } while (isset($responseArr['PurchaseOrders']) && count($responseArr['PurchaseOrders']) > 0);
    }

    protected function syncDependencies($apiId, $parameters)
    {
        $organisations = new Organisations;

        $organisations->sync($apiId);

        $contactGroups = new ContactGroups;

        $contactGroups->sync($apiId);

        $contacts = new Contacts;

        $contacts->sync($apiId, $parameters);

        $items = new Items;

        $items->sync($apiId);
    }

    protected function getPurchaseOrderAttachments($purchaseOrderId)
    {
        $request = new GetPurchaseOrderAttachmentsRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $this->xeroApi->getPurchaseOrderAttachments($request);

        $responseArr = $response->toArray();

        if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
            if (isset($responseArr['Attachments'])) {
                return $responseArr['Attachments'];
            }
        }

        return [];
    }

    protected function getPurchaseOrderHistory($purchaseOrderId)
    {
        $request = new GetPurchaseOrderHistoryRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $this->xeroApi->getPurchaseOrderHistory($request);

        $responseArr = $response->toArray();

        if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
            if (isset($responseArr['HistoryRecords'])) {
                return $responseArr['HistoryRecords'];
            }
        }

        return [];
    }

    protected function addUpdateXeroPurchaseOrders($apiId, array $purchaseOrders)
    {
        foreach ($purchaseOrders as $purchaseOrderKey => $purchaseOrder) {
            $model = SystemApiXeroPurchaseOrders::class;

            $xeroPo = $model::findFirst(
                [
                    'conditions'    => 'PurchaseOrderID = :poid:',
                    'bind'          =>
                        [
                            'poid'  => $purchaseOrder['PurchaseOrderID']
                        ]
                ]
            );

            $purchaseOrder['api_id'] = $apiId;

            $purchaseOrder['ContactID'] = $purchaseOrder['Contact']['ContactID'];

            if (!$xeroPo) {
                $modelToUse = new $model();

                $modelToUse->assign($this->jsonData($purchaseOrder));

                $modelToUse->create();

                $this->addCounter = $this->addCounter + 1;

                array_push($this->addedIds, $purchaseOrder['PurchaseOrderID']);

                $thisPo = $modelToUse->toArray();
            } else {
                if ($purchaseOrder['UpdatedDateUTC'] !== $xeroPo->UpdatedDateUTC) {

                    $xeroPo->assign($this->jsonData($purchaseOrder));

                    $xeroPo->update();

                    $this->updateCounter = $this->updateCounter + 1;

                    array_push($this->updatedIds, $purchaseOrder['PurchaseOrderID']);

                    $thisPo = $xeroPo->toArray();
                } else {
                    continue;
                }
            }

            if (isset($purchaseOrder['LineItems']) && count($purchaseOrder['LineItems']) > 0) {
                $this->addUpdateXeroPurchaseOrderLineItems($purchaseOrder);
            }

            if (isset($purchaseOrder['HasAttachments']) && $purchaseOrder['HasAttachments'] == true) {
                $xeroAttachments = new Attachments;

                $xeroAttachments->sync(
                    $apiId,
                    $this->packageName,
                    $thisPo['PurchaseOrderID'],
                    $this->getPurchaseOrderAttachments($thisPo['PurchaseOrderID'])
                );
            }

            $xeroHistory = new History;

            $xeroHistory->sync(
                $apiId,
                $this->packageName,
                $thisPo['PurchaseOrderID'],
                $this->getPurchaseOrderHistory($thisPo['PurchaseOrderID'])
            );
        }
    }

    protected function addUpdateXeroPurchaseOrderLineItems($purchaseOrder)
    {
        if (isset($purchaseOrder['LineItems']) && count($purchaseOrder['LineItems']) > 0) {
            foreach ($purchaseOrder['LineItems'] as $lineItemKey => $lineItem) {
                $model = SystemApiXeroPurchaseOrdersLineitems::class;

                $xeroPo = $model::findFirst(
                    [
                        'conditions'    => 'PurchaseOrderID = :poid: AND LineItemID = :liid:',
                        'bind'          =>
                            [
                                'poid'  => $purchaseOrder['PurchaseOrderID'],
                                'liid'  => $lineItem['LineItemID']
                            ]
                    ]
                );

                $modelToUse = new $model();

                $modelToUse->assign($lineItem);

                if (!$xeroPo) {
                    $modelToUse->assign($purchaseOrder);

                    $modelToUse->create();
                } else if ($xeroPo && $xeroPo->count() > 0) {
                    $modelToUse->assign($purchaseOrder);

                    $modelToUse->update();
                }
            }
        }
    }
}