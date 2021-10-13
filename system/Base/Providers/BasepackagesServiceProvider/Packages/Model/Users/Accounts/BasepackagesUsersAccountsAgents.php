<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;

class BasepackagesUsersAccountsAgents extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $account_id;

    public $session_id;

    public $client_address;

    public $user_agent;

    public $verified;

    public $verification_code;

    public function initialize()
    {
        $this->modelRelations['sessions']['relationObj'] = $this->belongsTo(
            'session_id',
            BasepackagesUsersAccountsSessions::class,
            'session_id'
        );

        parent::initialize();
    }


    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}