<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;

class BasepackagesUsersAccountsAgents extends BaseModel
{
    public $id;

    public $session_id;

    public $client_address;

    public $user_agent;

    public $verified;

    public function initialize()
    {
        $this->belongsTo(
            'session_id',
            BasepackagesUsersAccountsSessions::class,
            'session_id'
        );
    }
}