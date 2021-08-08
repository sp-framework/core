<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrders;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrdersAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrdersHistoryRecords;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrdersLineitems;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest;
use System\Base\BasePackage;

class PurchaseOrders extends BasePackage
{
    protected $apiPackage;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetPurchaseOrdersRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        foreach ($xeroApis as $key => $xeroApi) {
            $this->syncWithXero($xeroApi['api_id']);
        }
    }

    protected function syncWithXero($apiId)
    {
        $api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->syncDependencies($apiId);

        $xeroAccountingApi = $api->useService('XeroAccountingApi');

        $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetPurchaseOrders', $apiId);

        if ($modifiedSince) {
            $xeroAccountingApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        $page = 1;

        do {
            $this->request->page = $page;

            $response = $xeroAccountingApi->getPurchaseOrders($this->request);

            $api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ($responseArr['Status'] === 'OK' && isset($responseArr['PurchaseOrders'])) {
                if (count($responseArr['PurchaseOrders']) > 0) {
                    foreach ($responseArr['PurchaseOrders'] as $purchaseOrderKey => &$purchaseOrder) {
                        $purchaseOrder['api_id'] = $xeroApi['id'];
                        $purchaseOrder['ContactID'] = $purchaseOrder['Contact']['ContactID'];
                        $purchaseOrder['resync_local'] = 1;

                        if ($purchaseOrder['HasAttachments'] === true) {
                            $purchaseOrder['Attachments'] =
                                $this->getPurchaseOrderAttachments($xeroAccountingApi, $purchaseOrder['PurchaseOrderID']);
                        }

                        $purchaseOrder['HistoryRecords'] =
                            $this->getPurchaseOrderHistory($xeroAccountingApi, $purchaseOrder['PurchaseOrderID']);
                    }

                    $this->addUpdateXeroPurchaseOrders($responseArr['PurchaseOrders']);
                }
            }

            $page++;
            var_dump($responseArr);
        } while (isset($responseArr['PurchaseOrders']) && count($responseArr['PurchaseOrders']) > 0);

    }

    protected function syncDependencies($apiId)
    {
        $contacts = new Contacts;

        $contacts->sync($apiId);
    }

    protected function getPurchaseOrderAttachments($xeroAccountingApi, $purchaseOrderId)
    {
        $request = new GetPurchaseOrderAttachmentsRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $xeroAccountingApi->getPurchaseOrderAttachments($request);

        $responseArr = $response->toArray();

        if ($responseArr['Status'] === 'OK') {
            return $responseArr['Attachments'];
        }

        return [];
    }

    protected function getPurchaseOrderHistory($xeroAccountingApi, $purchaseOrderId)
    {
        $request = new GetPurchaseOrderHistoryRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $xeroAccountingApi->getPurchaseOrderHistory($request);

        $responseArr = $response->toArray();

        if ($responseArr['Status'] === 'OK') {
            return $responseArr['HistoryRecords'];
        }

        return [];
    }

    protected function addUpdateXeroPurchaseOrders(array $purchaseOrders)
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

            $modelToUse = new $model();

            if (!$xeroPo) {
                $modelToUse->assign($purchaseOrder);

                $modelToUse->create();
            } else if ($xeroPo && $xeroPo->count() > 0) {
                if ($purchaseOrder['UpdatedDateUTC'] !== $xeroPo->UpdatedDateUTC) {
                    $modelToUse->assign($purchaseOrder);

                    $modelToUse->update();
                }
            }

            $this->addUpdateXeroPurchaseOrderLineItems($purchaseOrder);

            $this->addUpdateXeroPurchaseOrderAttachments($purchaseOrder);

            $this->addUpdateXeroPurchaseOrderHistoryRecords($purchaseOrder);
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

    protected function addUpdateXeroPurchaseOrderAttachments($purchaseOrder)
    {
        if (isset($purchaseOrder['Attachments']) && count($purchaseOrder['Attachments']) > 0) {
            foreach ($purchaseOrder['Attachments'] as $attachmentKey => $attachment) {
                $model = SystemApiXeroPurchaseOrdersAttachments::class;

                $xeroPo = $model::findFirst(
                    [
                        'conditions'    => 'PurchaseOrderID = :poid: AND AttachmentID = :aid:',
                        'bind'          =>
                            [
                                'poid'  => $purchaseOrder['PurchaseOrderID'],
                                'aid'   => $attachment['AttachmentID']
                            ]
                    ]
                );

                $modelToUse = new $model();

                $modelToUse->assign($attachment);

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

    protected function addUpdateXeroPurchaseOrderHistoryRecords($purchaseOrder)
    {
        if (isset($purchaseOrder['HistoryRecords']) && count($purchaseOrder['HistoryRecords']) > 0) {
            foreach ($purchaseOrder['HistoryRecords'] as $historyRecordKey => $historyRecord) {
                $model = SystemApiXeroPurchaseOrdersHistoryRecords::class;

                $xeroPo = $model::findFirst(
                    [
                        'conditions'    => 'PurchaseOrderID = :poid: AND DateUTC = :dutc:',
                        'bind'          =>
                            [
                                'poid'  => $purchaseOrder['PurchaseOrderID'],
                                'dutc'  => $historyRecord['DateUTC']
                            ]
                    ]
                );

                $modelToUse = new $model();

                $modelToUse->assign($historyRecord);

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