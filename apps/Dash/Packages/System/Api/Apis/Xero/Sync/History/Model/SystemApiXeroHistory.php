<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model;

use System\Base\BaseModel;

class SystemApiXeroPurchaseOrdersHistoryRecords extends BaseModel
{
    public $id;

    public $PurchaseOrderID;

    public $Details;

    public $Changes;

    public $User;

    public $DateUTC;

    public $DateUTCString;
}