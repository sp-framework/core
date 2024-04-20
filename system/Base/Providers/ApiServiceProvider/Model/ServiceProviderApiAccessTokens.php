<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use System\Base\BaseModel;

class ServiceProviderApiAccessTokens extends BaseModel implements AccessTokenEntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait;

    public $id;

    public $api_id;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $client_id;

    public $access_token;

    public $expires;

    public $revoked;

    public function getUserIdentifier()
    {
        return $this->account_id;
    }

    public function setUserIdentifier($identifier)
    {
        $this->account_id = $identifier;
    }

    public function addScope($scope)
    {
        $this->scopes[$scope] = $scope;
    }
}