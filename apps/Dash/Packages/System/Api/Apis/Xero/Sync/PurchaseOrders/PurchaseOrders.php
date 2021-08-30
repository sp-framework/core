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

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetPurchaseOrdersRestRequest;

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

        $this->syncDependencies($apiId);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetPurchaseOrders', $apiId);

        if ($modifiedSince) {
            $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
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

    protected function syncDependencies($apiId)
    {
        $organisations = new Organisations;

        $organisations->sync($apiId);

        $contactGroups = new ContactGroups;

        $contactGroups->sync($apiId);

        $contacts = new Contacts;

        $contacts->sync($apiId);

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

                $thisPo = $modelToUse->toArray();
            } else {
                if ($purchaseOrder['UpdatedDateUTC'] !== $xeroPo->UpdatedDateUTC) {

                    $modelToUse->assign($this->jsonData($purchaseOrder));

                    $modelToUse->update();

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