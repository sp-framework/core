<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class Filters extends BaseModel
{
    public $id;

    public $name;

    public $component_id;

    public $conditions;

    public $type;

    public $auto_generated;

    public $is_default;

    public $account_id;

    public $shared_ids;
}