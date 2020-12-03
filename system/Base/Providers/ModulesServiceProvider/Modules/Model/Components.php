<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Components extends BaseModel
{
    public $id;

    public $route;

    public $name;

    public $description;

    public $category;

    public $subcategory;

    public $version;

    public $repo;

    public $class;

    public $settings;

    public $dependencies;

    public $menu;

    public $applications;

    public $files;

    public $update_available;

    public $update_version;
}