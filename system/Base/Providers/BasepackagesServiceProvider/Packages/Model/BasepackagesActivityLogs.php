<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesActivityLogs extends BaseModel
{
    public $id;

    public $activity_type;

    public $account_id;

    public $created_at;

    public $package_name;

    public $package_row_id;

    public $log;
}