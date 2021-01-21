<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class Accounts extends BaseModel
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

    public function initialize()
    {
        //
    }
}
