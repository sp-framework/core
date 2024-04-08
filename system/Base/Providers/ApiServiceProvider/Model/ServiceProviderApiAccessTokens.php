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

    public $app_id;

    public $domain_id;

    public $access_token;

    public $expires;

    public $scope;

    public $client_id;

    public $account_id;

    public $revoked;

    public $created_at;

    public $updated_at;

    public function getUserIdentifier()
    {
        return $this->account_id;
    }
}