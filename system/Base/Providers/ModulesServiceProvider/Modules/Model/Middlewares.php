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

    public $path;

    public $class;

    public $settings;

    public $dependencies;

    public $application_id;

    public $sequence;

    public $installed;

    public $files;

    public $enabled;

    public $update_available;

    public $update_version;
}