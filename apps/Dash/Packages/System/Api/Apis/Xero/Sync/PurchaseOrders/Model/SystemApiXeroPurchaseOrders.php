<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model;

use System\Base\BaseModel;

class SystemApiXeroPurchaseOrders extends BaseModel
{
    public $id;

    public $baz_po_id;

    public $api_id;

    public $PurchaseOrderID;

    public $ContactID;

    public $PurchaseOrderNumber;

    public $Date;

    public $DeliveryDate;

    public $AttentionTo;

    public $Telephone;

    public $DeliveryInstructions;

    public $HasErrors;

    public $IsDiscounted;

    public $SentToContact;

    public $Reference;

    public $Type;

    public $CurrencyRate;

    public $CurrencyCode;

    public $BrandingThemeID;

    public $Status;

    public $LineAmountTypes;

    public $SubTotal;

    public $TotalTax;

    public $Total;

    public $UpdatedDateUTC;

    public $HasAttachments;

    public $DeliveryAddress;

    public $ExpectedArrivalDate;

    public $ExpectedArrivalDateString;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}