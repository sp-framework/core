<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesViews extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $app_type;

    public $category;

    public $sub_category;

    public $version;

    public $repo;

    public $settings;

    public $dependencies;

    public $apps;

    public $installed;

    public $files;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $repo_details;
}