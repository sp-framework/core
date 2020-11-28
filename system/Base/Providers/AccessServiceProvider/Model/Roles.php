<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class Roles extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $parent_id;

    public $permissions;

    public $accounts;
}