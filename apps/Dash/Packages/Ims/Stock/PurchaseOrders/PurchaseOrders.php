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
            $this->addResponse('Added purchase order.');
        } else {
            $this->addResponse('Error adding purchase order.', 1);
        }
    }

    public function updatePurchaseOrder(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated purchase order.');
        } else {
            $this->addResponse('Error updating purchase order.', 1);
        }
    }

    public function removePurchaseOrder(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->addResponse('Removed purchase order.');
        } else {
            $this->addResponse('Error removing purchase order.', 1);
        }
    }

    public function getOrderStatuses()
    {
        return
            [
                '1'     => [
                    'id'    => '1',
                    'name'  => 'DRAFT',
                ],
                '2'     => [
                    'id'    => '2',
                    'name'  => 'SUBMITTED',
                ],
                '3'     => [
                    'id'    => '3',
                    'name'  => 'AUTHORISED',
                ],
                '4'     => [
                    'id'    => '4',
                    'name'  => 'BILLED',
                ],
                '5'     => [
                    'id'    => '5',
                    'name'  => 'DELETED'
                ]
            ];
    }
}