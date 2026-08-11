<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use System\Base\BaseModel;

class ServiceProviderApiUsers extends BaseModel implements UserEntityInterface
{
    public $account_id;

    public function getIdentifier() :string
    {
        return $this->account_id ?? '';
    }
}