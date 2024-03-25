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

    public $two_fa_totp_status;

    public $two_fa_totp_secret;

    public $two_fa_email_code;

    public $two_fa_email_code_sent_on;

    public $password_history;

    public $password_set_on;

    public $force_pwreset_after;

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