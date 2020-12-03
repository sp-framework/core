<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Middlewares extends BaseModel
{
    public $id;

    public $name;

    public $display_name;

    public $description;

    public $version;

    public $repo;

    public $class;

    public $settings;

    public $applications;

    public $files;

    public $update_available;

    public $update_version;
}