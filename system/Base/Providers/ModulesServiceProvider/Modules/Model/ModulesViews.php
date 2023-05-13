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

    public $version;

    public $repo;

    public $settings;

    public $user_settings;

    public $dependencies;

    public $apps;

    public $installed;

    public $files;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $level_of_update;

    public $auto_update;

    public $repo_details;
}