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

class BasepackagesUsersAccounts extends BaseModel
{
    public $id;

    public $status;

    public $email;

    public $domain;

    public $package_name;

    public $package_row_id;

    public function initialize()
    {
        self::$modelRelations['security']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsSecurity::class,
            'account_id',
            [
                'alias'         => 'security'
            ]
        );

        self::$modelRelations['canlogin']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsCanlogin::class,
            'account_id',
            [
                'alias'         => 'canlogin'
            ]
        );

        self::$modelRelations['sessions']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            [
                'alias'         => 'sessions'
            ]
        );

        self::$modelRelations['identifiers']['relationObj'] = $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            'session_id',
            BasepackagesUsersAccountsIdentifiers::class,
            'session_id',
            [
                'alias'         => 'identifiers'
            ]
        );

        self::$modelRelations['agents']['relationObj'] = $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            'session_id',
            BasepackagesUsersAccountsAgents::class,
            'session_id',
            [
                'alias'         => 'agents'
            ]
        );

        self::$modelRelations['tunnels']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersAccountsTunnels::class,
            'account_id',
            [
                'alias'         => 'tunnels'
            ]
        );

        self::$modelRelations['profiles']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesUsersProfiles::class,
            'account_id',
            [
                'alias'         => 'profiles'
            ]
        );

        parent::initialize();
    }
}
