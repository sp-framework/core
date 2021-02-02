<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;

class BasepackagesUsersRoles extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $parent_id;

    public $permissions;

    public $accounts;
}