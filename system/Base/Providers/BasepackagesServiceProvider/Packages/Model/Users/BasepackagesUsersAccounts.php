<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;

class BasepackagesUsersAccounts extends BaseModel
{
    public $id;

    public $email;

    public $domain;

    public $password;

    public $role_id;

    public $override_role;

    public $permissions;

    public $can_login;

    public $force_pwreset;

    public $session_ids;

    public $remember_identifier;

    public $remember_token;

    public $two_fa_status;

    public $two_fa_secret;

    public function initialize()
    {
        //
    }
}
