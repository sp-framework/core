<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Model;

use System\Base\BaseModel;

class SystemApiXeroItems extends BaseModel
{
    public $id;

    public $baz_product_id;

    public $api_id;

    public $ItemID;

    public $Code;

    public $Name;

    public $IsPurchased;

    public $IsSold;

    public $IsTrackedAsInventory;

    public $InventoryAssetAccountCode;

    public $TotalCostPool;

    public $QuantityOnHand;

    public $Description;

    public $PurchaseDescription;

    public $SalesDetails;

    public $PurchaseDetails;

    public $UpdatedDateUTC;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}