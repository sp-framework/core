<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesExternals extends BaseModel
{
    public $id;

    public $developer;

    public $name;

    public $display_name;

    public $description;

    public $module_type;

    public $app_type;

    public $abandoned;

    public $version;

    public $patches;

    public $installed;

    public $required_by;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $notification_subscriptions;
}