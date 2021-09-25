<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;

class BasepackagesUsersAccountsIdentifiers extends BaseModel
{
    protected static $modelRelations = [];

    public $id;

    public $account_id;

    public $app;

    public $session_id;

    public $identifier;

    public $token;

    public function initialize()
    {
        self::$modelRelations['sessions']['relationObj'] = $this->belongsTo(
            'session_id',
            BasepackagesUsersAccountsSessions::class,
            'session_id'
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return self::$modelRelations;
    }
}