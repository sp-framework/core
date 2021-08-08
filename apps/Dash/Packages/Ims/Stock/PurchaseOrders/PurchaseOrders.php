<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrders;
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
}