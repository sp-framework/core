<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesViews extends BaseModel
{
    public $id;

    public $name;

    public $display_name;

    public $description;

    public $module_type;

    public $app_type;

    public $category;

    public $version;

    public $view_modules_version;

    public $base_view_module_id;

    public $is_subview;

    public $repo;

    public $settings;

    public $user_settings;

    public $dependencies;

    public $apps;

    public $installed;

    public $files;

    public $api_id;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $level_of_update;

    public $auto_update;

    public $repo_details;
}