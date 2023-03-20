<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesDashboards extends BaseModel
{
    public $id;

    public $name;

    public $app_id;

    public $created_by;

    public $settings;
}