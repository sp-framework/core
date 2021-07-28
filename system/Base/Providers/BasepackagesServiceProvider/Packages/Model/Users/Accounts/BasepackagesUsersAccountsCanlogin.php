<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersAccountsCanlogin extends BaseModel
{
    public $id;

    public $account_id;

    public $app;

    public $allowed;

    public function initialize()
    {
        $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id'
        );
    }
}