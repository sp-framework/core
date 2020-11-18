<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Applications extends BaseModel
{
    public $id;

    public $name;

    public $route;

    public $display_name;

    public $description;

    public $version;

    public $repo;

    public $settings;

    public $dependencies;

    public $is_default;

    public $installed;

    public $files;

    public $update_available;

    public $update_version;

    public $mode;
}