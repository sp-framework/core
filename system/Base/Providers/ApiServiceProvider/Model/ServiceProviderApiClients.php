<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use System\Base\BaseModel;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAccessTokens;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAuthorizationCodes;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiRefreshTokens;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class ServiceProviderApiClients extends BaseModel implements ClientEntityInterface
{
    use ClientTrait, EntityTrait;

    //Note: name and redirectUri properties defined in ClientTrait
    public $id;

    public $api_id;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $email;

    public $client_id;

    public $device_id;

    public $client_secret;

    public $last_used;

    public $revoked;

    public $concurrent_calls_count;

    // public $concurrent_calls_connections;

    public $per_minute_calls_count;

    public $per_minute_calls_start;

    // public $per_minute_calls_connections;

    public $per_hour_calls_count;

    public $per_hour_calls_start;

    // public $per_hour_calls_connections;

    public $per_day_calls_count;

    public $per_day_calls_start;

    // public $per_day_calls_connections;

    public function getIdentifier()
    {
        return $this->client_id;
    }

    public function getUserIdentifier()
    {
        return $this->account_id;
    }

    public function setRedirectUri(string $uri)
    {
        $this->redirectUri = $uri;
    }

    public function setIdentifier($identifier)
    {
        $this->client_id = $identifier;

        return $this;
    }

    public function setConfidential()
    {
        $this->isConfidential = true;
    }
}