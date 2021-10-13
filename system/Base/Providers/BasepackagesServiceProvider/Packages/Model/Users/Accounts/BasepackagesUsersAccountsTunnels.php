<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersAccountsTunnels extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $account_id;

    public $notifications_tunnel;

    public $messenger_tunnel;

    public function initialize()
    {
        $this->modelRelations['account']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id'
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}