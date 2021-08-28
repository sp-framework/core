<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model;

use System\Base\BaseModel;

class ImsStockPurchaseOrdersProducts extends BaseModel
{
    public $id;

    public $purchase_order_id;

    public $seq;

    public $mpn;

    public $product_title;

    public $use_vendor_tax;

    public $tax;

    public $tax_rate;

    public $use_vendor_discount;

    public $product_discount;

    public $product_discount_rate;

    public $product_qty;

    public $product_unit_price;

    public $product_unit_price_incl_tax;

    public $product_amount;
}