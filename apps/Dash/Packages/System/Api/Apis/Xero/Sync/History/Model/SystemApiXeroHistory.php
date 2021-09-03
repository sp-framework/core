<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model;

use System\Base\BaseModel;

class SystemApiXeroHistory extends BaseModel
{
    public $id;

    public $baz_note_id;

    public $api_id;

    public $xero_package;

    public $xero_package_row_id;

    public $Changes;

    public $Details;

    public $User;

    public $DateUTC;

    public $DateUTCString;

    public $resync_local;

    public $resync_remote;
}