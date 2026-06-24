<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiUsers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesContactBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsEnv;
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

    public $profile_package_class;

    public $profile_package_row_id;

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
                'alias'         => 'identifier',
                'params'        => [
                    'conditions'    => 'session_id = :session_id:',
                    'bind'          => [
                        'session_id'       => $this->getDi()->getShared('session')->getId()
                    ]
                ]
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

        $this->modelRelations['contact']['relationObj'] = $this->hasOneThrough(
            'id',
            BasepackagesUsersProfiles::class,
            'account_id',
            'id',
            BasepackagesContactBook::class,
            'package_row_id',
            [
                'alias'         => 'contact'
            ]
        );

        $account_id = '0';
        if (isset($this->access->auth) && $this->access->auth->account()) {
            $account_id = $this->access->auth->account()['id'];
        }
        $this->modelRelations['api_clients']['relationObj'] = $this->hasMany(
            'id',
            ServiceProviderApiClients::class,
            'account_id',
            [
                'alias'         => 'api_clients',
                'params'        => [
                    'conditions'    => 'revoked = :revoked:',
                    'bind'          => [
                        'revoked'       => '0'
                    ]
                ]
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

        $this->modelRelations['env']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsEnv::class,
            'account_id',
            [
                'alias'         => 'env'
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
