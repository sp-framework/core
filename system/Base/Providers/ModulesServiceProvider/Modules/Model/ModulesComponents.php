<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesComponents extends BaseModel
{
    public $id;

    public $name;

    public $route;

    public $alias;

    public $description;

    public $app_type;

    public $category;

    public $subcategory;

    public $version;

    public $repo;

    public $class;

    public $settings;

    public $dependencies;

    public $menu;

    public $menu_id;

    public $installed;

    public $apps;

    public $files;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $level_of_update;

    public $auto_update;

    public $repo_details;
}