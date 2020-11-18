<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Views extends BaseModel
{
    public $id;

    public $name;

    public $display_name;

    public $description;

    public $version;

    public $repo;

    public $settings;

    public $dependencies;

    public $application_id;

    public $installed;

    public $files;

    public $update_available;

    public $update_version;
}