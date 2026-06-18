<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesMutex extends BaseModel
{
    public $id;

    public $package_class;

    public $package_row_id;

    public $parent_lock_id;

    public $account_id;

    public $locked_at;
}