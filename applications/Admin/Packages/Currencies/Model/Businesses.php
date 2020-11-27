<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class Filters extends BaseModel
{
    public $id;

    public $name;

    public $conditions;

    public $component_id;

    public $permission;

    public $is_default;

    public $shared_ids;
}