<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiUsers;
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

        $this->modelRelations['identifier']['relationObj'] = $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            ['account_id', 'session_id'],
            BasepackagesUsersAccountsIdentifiers::class,
            ['account_id', 'session_id'],
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

        $this->modelRelations['tunnels']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsTunnels::class,
            'account_id',
            [
                'alias'         => 'tunnels'
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

        $apiName = '';
        if ($this->apps && $this->apps->getAppInfo() && $this->domains->domain && $this->auth->account()) {
            $apiName = $this->apps->getAppInfo()['id'] . '_' . $this->domains->domain['id'] . '_' . $this->auth->account()['id'];
        }
        $this->modelRelations['api_client']['relationObj'] = $this->hasOne(
            'id',
            ServiceProviderApiClients::class,
            'account_id',
            [
                'alias'         => 'api_client',
                'params'        => [
                    'conditions'    => 'name = :apiName:',
                    'bind'          => [
                        'apiName'  => $apiName
                    ]
                ]
            ]
        );

        $this->modelRelations['api_user']['relationObj'] = $this->hasOne(
            'id',
            ServiceProviderApiUsers::class,
            'account_id',
            [
                'alias'         => 'api_user'
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
