<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesMaintenance extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $status;

    public $state;

    public $Reference;

    public $start_at;

    public $end_at;

    public $level_of_maintenance;

    public $level_of_maintenance_modules;

    public $allow_from_ip_addresses;

    public $notification_email_groups;

    public $notification_email_users;

    public $notification_email_others;

    public $maintenance_template;
}