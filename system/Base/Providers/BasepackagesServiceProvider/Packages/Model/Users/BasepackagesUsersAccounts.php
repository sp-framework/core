<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSecurity;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersRoles;

class BasepackagesUsersAccounts extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $status;

    public $email;

    public $username;

    public $domain;

    public $package_name;

    public $package_row_id;

    public function initialize()
    {
        $this->modelRelations['security']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsSecurity::class,
            'account_id',
            [
                'alias'         => 'security'
            ]
        );

        $this->modelRelations['canlogin']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsCanlogin::class,
            'account_id',
            [
                'alias'         => 'canlogin'
            ]
        );

        $this->modelRelations['sessions']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            [
                'alias'         => 'sessions'
            ]
        );

        $this->modelRelations['identifier']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsIdentifiers::class,
            'account_id',
            [
                'alias'         => 'identifier'
            ]
        );

        $this->modelRelations['agents']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsAgents::class,
            'account_id',
            [
                'alias'         => 'agents'
            ]
        );

        $this->modelRelations['tunnel']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsTunnels::class,
            'account_id',
            [
                'alias'         => 'tunnel'
            ]
        );

        $this->modelRelations['profile']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersProfiles::class,
            'account_id',
            [
                'alias'         => 'profile'
            ]
        );

        $this->modelRelations['role']['relationObj'] = $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSecurity::class,
            'account_id',
            'role_id',
            BasepackagesUsersRoles::class,
            'id',
            [
                'alias'         => 'role'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}
