<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model;

use System\Base\BaseModel;

class SystemApiXeroPurchaseOrdersLineitems extends BaseModel
{
    public $id;

    public $PurchaseOrderID;

    public $LineItemID;

    public $Description;

    public $Quantity;

    public $UnitAmount;

    public $ItemCode;

    public $AccountID;

    public $AccountCode;

    public $Tracking;

    public $TaxType;

    public $RepeatingInvoiceID;

    public $TaxAmount;

    public $DiscountRate;

    public $LineAmount;
}