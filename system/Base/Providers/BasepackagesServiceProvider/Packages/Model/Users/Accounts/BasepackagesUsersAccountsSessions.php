<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersAccountsSessions extends BaseModel
{
    protected static $modelRelations = [];

    public $id;

    public $account_id;

    public $app;

    public $session_id;

    public function initialize()
    {
        self::$modelRelations['accounts']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id'
        );

        self::$modelRelations['identifiers']['relationObj'] = $this->hasOne(
            'session_id',
            BasepackagesUsersAccountsIdentifiers::class,
            'session_id',
            [
                'alias'         => 'identifiers'
            ]
        );

        self::$modelRelations['sessions']['relationObj'] = $this->hasOne(
            'session_id',
            BasepackagesUsersAccountsAgents::class,
            'session_id'
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return self::$modelRelations;
    }
}