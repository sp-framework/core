<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model;

use System\Base\BaseModel;

class SystemApiXeroContactGroups extends BaseModel
{
    public $id;

    public $api_id;

    public $baz_vendor_group_id;

    public $ContactGroupID;

    public $Name;

    public $Status;

    public $HasValidationErrors;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}