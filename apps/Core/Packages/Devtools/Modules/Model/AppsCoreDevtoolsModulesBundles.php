<?php

namespace Apps\Core\Packages\Devtools\Modules\Model;

use System\Base\BaseModel;

class AppsCoreDevtoolsModulesBundles extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $module_type;

    public $app_type;

    public $api_id;

    public $repo;

    public $bundle_modules;
}