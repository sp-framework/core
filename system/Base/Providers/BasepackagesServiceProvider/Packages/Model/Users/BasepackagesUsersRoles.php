<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSecurity;

class BasepackagesUsersRoles extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $description;

    public $type;

    public $permissions;

    public function initialize()
    {
        $this->modelRelations['accounts']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsSecurity::class,
            'role_id',
            [
                'alias'         => 'accounts'
            ]
        );
    }
}