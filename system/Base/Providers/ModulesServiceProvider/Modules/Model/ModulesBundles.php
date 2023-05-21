<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesBundles extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $module_type;

    public $app_type;

    public $api_id;

    public $repo;

    public $bundle_modules;

    public $updated_by;

    public $updated_on;
}