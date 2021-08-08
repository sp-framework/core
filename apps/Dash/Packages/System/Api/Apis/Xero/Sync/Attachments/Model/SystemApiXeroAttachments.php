<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroAttachments extends BaseModel
{
    public $id;

    public $xero_package;

    public $xero_package_row_id;

    public $AttachmentID;

    public $FileName;

    public $Url;

    public $MimeType;

    public $ContentLength;

    public $IncludeOnline;
}