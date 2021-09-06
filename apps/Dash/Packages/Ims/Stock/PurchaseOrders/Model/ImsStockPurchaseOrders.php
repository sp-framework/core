<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model;

use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrdersProducts;
use System\Base\BaseModel;

class ImsStockPurchaseOrders extends BaseModel
{
    public $id;

    public $ref_id;

    public $entity_id;

    public $references;

    public $status;

    public $sent;

    public $vendor_id;

    public $vendor_address_id;

    public $vendor_contact_id;

    public $delivery_date;

    public $delivery_type;

    public $entity_location_id;

    public $location_contact_id;

    public $location_address_id;

    public $customer_id;

    public $customer_address_id;

    public $one_off_address_id;

    public $address_id;

    public $contact_fullname;

    public $contact_phone;

    public $total_quantity;

    public $total_tax;

    public $total_discount;

    public $total_amount;

    public $delivery_instructions;

    public $attachments;

    public function initialize()
    {
        $this->hasMany(
            'id',
            ImsStockPurchaseOrdersProducts::class,
            'purchase_order_id',
            [
                'alias' => 'products'
            ]
        );
    }
}