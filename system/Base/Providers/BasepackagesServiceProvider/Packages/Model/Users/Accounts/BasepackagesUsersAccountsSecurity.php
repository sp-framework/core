<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersAccountsSecurity extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $account_id;

    public $password;

    public $role_id;

    public $override_role;

    public $permissions;

    public $force_pwreset;

    public $twofa_otp_status;

    public $twofa_otp_secret;

    public $twofa_otp_hotp_counter;

    public $twofa_email_code;

    public $twofa_email_code_sent_on;

    public $password_history;

    public $password_set_on;

    public $force_pwreset_after;

    public $forgotten_request;

    public $forgotten_request_session_id;

    public $forgotten_request_ip;

    public $forgotten_request_agent;

    public $forgotten_request_code;

    public $forgotten_request_sent_on;

    public function initialize()
    {
        $this->modelRelations['accounts']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id'
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