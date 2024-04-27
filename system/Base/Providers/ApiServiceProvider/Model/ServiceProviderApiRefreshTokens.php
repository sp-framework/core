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

    public $api_id;

    public $app_id;

    public $domain_id;

    public $client_id;

    public $account_id;

    public $refresh_token;

    public $expires;

    public $revoked;
}
