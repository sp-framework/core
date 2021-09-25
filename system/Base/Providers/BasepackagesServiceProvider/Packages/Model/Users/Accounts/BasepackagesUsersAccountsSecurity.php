<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersAccountsSecurity extends BaseModel
{
    protected static $modelRelations = [];

    public $id;

    public $account_id;

    public $password;

    public $role_id;

    public $override_role;

    public $permissions;

    public $force_pwreset;

    public $two_fa_status;

    public $two_fa_secret;

    public function initialize()
    {
        self::$modelRelations['accounts']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id'
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return self::$modelRelations;
    }
}