<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesNotifications extends BaseModel
{
    public $id;

    public $notification_type;

    public $notification;

    public $account_id;

    public $read;

    public $archive;

    public $created_at;
}