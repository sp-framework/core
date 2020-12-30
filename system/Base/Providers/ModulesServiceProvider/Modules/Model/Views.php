<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Views extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $category;

    public $sub_category;

    public $version;

    public $repo;

    public $settings;

    public $dependencies;

    public $applications;

    public $installed;

    public $files;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;
}