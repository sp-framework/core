<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model;

use System\Base\BaseModel;

class SystemApiXeroPurchaseOrdersAttachments extends BaseModel
{
    public $id;

    public $PurchaseOrderID;

    public $AttachmentID;

    public $FileName;

    public $Url;

    public $MimeType;

    public $ContentLength;

    public $IncludeOnline;
}