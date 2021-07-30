<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrders;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class PurchaseOrders extends BasePackage
{
    protected $modelToUse = ImsStockPurchaseOrders::class;

    protected $packageName = 'purchaseOrders';

    public $purchaseOrders;

    public function addPurchaseOrder(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added purchase order.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding purchase order.';
        }
    }

    public function updatePurchaseOrder(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated purchase order.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating purchase order.';
        }
    }

    public function removePurchaseOrder(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed purchase order.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing purchase order.';
        }
    }

    public function syncWithXero()
    {
        $apiPackage = new Api;

        $request = new GetPurchaseOrdersRestRequest;

        $xeroApis = $apiPackage->getApiByType('xero', true);

        foreach ($xeroApis as $key => $xeroApi) {
            $api = $apiPackage->useApi(['api_id' => $xeroApi['id']]);

            $xeroAccountingApi = $api->useService('XeroAccountingApi');

            // $xeroAccountingApi->setOptionalHeader(['if-modified-since' => '1615808940294']);

            $response = $xeroAccountingApi->getPurchaseOrders($request);
            $api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ($responseArr['Status'] === 'OK') {

                $purchaseOrders = $responseArr['PurchaseOrders'];

                if (count($purchaseOrders) > 0) {
                    foreach ($purchaseOrders as $purchaseOrderKey => &$purchaseOrder) {
                        if ($purchaseOrder['HasAttachments'] === true) {
                            $purchaseOrder['Attachments'] =
                                $this->getPurchaseOrderAttachments($xeroAccountingApi, $purchaseOrder['PurchaseOrderID']);
                        }

                        $purchaseOrder['HistoryRecords'] =
                            $this->getPurchaseOrderHistory($xeroAccountingApi, $purchaseOrder['PurchaseOrderID']);
                    }
                }
            }

            var_dump($responseArr);die();
        }
    }

    protected function getPurchaseOrderAttachments($xeroAccountingApi, $purchaseOrderId)
    {
        $request = new GetPurchaseOrderAttachmentsRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $xeroAccountingApi->getPurchaseOrderAttachments($request);

        $responseArr = $response->toArray();

        if ($responseArr['Status'] === 'OK') {
            return ['Attachments'];
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
}