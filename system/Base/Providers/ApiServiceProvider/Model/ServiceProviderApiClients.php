<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use System\Base\BaseModel;

class ServiceProviderApiClients extends BaseModel implements ClientEntityInterface
{
    use ClientTrait, EntityTrait;

    public $id;

    public $api_id;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $client_id;

    public $client_secret;

    public $grant_type;

    public $scope;

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