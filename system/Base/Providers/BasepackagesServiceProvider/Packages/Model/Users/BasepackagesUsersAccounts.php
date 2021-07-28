<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;

class BasepackagesUsersAccounts extends BaseModel
{
    public $id;

    public $email;

    public $domain;

    public $password;

    public $role_id;

    public $override_role;

    public $permissions;

    // public $can_login;

    public $force_pwreset;

    // public $session_ids;

    // public $remember_identifier;

    // public $remember_token;

    public $two_fa_status;

    public $two_fa_secret;

    // public $agents;

    // public $notifications_tunnel_id;

    // public $messenger_tunnel_id;

    public function initialize()
    {
        $this->hasMany(
            'id',
            BasepackagesUsersAccountsCanlogin::class,
            'account_id',
            [
                'alias' => 'canlogin'
            ]
        );

        $this->hasMany(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            [
                'alias' => 'sessions'
            ]
        );

        $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            'session_id',
            BasepackagesUsersAccountsIdentifiers::class,
            'session_id',
            [
                'alias' => 'identifiers'
            ]
        );

        $this->hasOneThrough(
            'id',
            BasepackagesUsersAccountsSessions::class,
            'account_id',
            'session_id',
            BasepackagesUsersAccountsAgents::class,
            'session_id',
            [
                'alias' => 'agents'
            ]
        );

        $this->hasOne(
            'id',
            BasepackagesUsersAccountsTunnels::class,
            'account_id',
            [
                'alias' => 'tunnels'
            ]
        );

        $this->hasOne(
            'id',
            BasepackagesUsersProfiles::class,
            'account_id',
            [
                'alias' => 'profiles'
            ]
        );
    }
}
