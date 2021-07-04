<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesNotifications extends BaseModel
{
    public $id;

    public $package_name;

    public $package_row_id;

    public $notification_type;

    public $app_id;

    public $account_id;

    public $created_by;

    public $created_at;

    public $notification_title;

    public $notification_details;

    public $read;

    public $archive;
}