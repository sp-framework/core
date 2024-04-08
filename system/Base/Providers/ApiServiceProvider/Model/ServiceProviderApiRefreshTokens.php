<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use System\Base\BaseModel;

class ServiceProviderApiRefreshTokens extends BaseModel implements RefreshTokenEntityInterface
{
    use RefreshTokenTrait, EntityTrait;

    public $id;

    public $app_id;

    public $domain_id;

    public $refresh_token;

    public $expires;

    public $client_id;

    public $scope;

    public $account_id;

    public $revoked;

    public $created_at;

    public $updated_at;

    public function setClientId($clientId)
    {
        $this->client_id = $clientId;

        return $this;
    }
}
