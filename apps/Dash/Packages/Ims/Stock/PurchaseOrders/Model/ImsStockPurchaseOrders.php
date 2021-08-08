<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model;

use System\Base\BaseModel;

class ImsStockPurchaseOrders extends BaseModel
{
    public $id;

    public $entity_id;

    public $reference_orders;

    public $expected_delivery_date;

    public $status;

    public $vendor_id;

    public $vendor_address_id;

    public $vendor_contact_id;
}