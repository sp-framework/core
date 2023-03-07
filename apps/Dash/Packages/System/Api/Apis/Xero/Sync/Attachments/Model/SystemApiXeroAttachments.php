<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model;

use System\Base\BaseModel;

class SystemApiXeroAttachments extends BaseModel
{
    public $id;

    public $baz_storage_local_id;

    public $api_id;

    public $xero_package;

    public $xero_package_row_id;

    public $AttachmentID;

    public $FileName;

    public $Url;

    public $MimeType;

    public $ContentLength;

    public $IncludeOnline;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}