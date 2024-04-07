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

    public $app_id;

    public $domain_id;

    public $account_id;

    public $client_id;

    // public $name;Defined in ClientTrait

    public $client_secret;

    public $redirect_uri;

    public $grant_types;

    public $scope;

    public $user_id;

    public $created_at;

    public $updated_at;

    public function getIdentifier()
    {
        return $this->client_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function setIdentifier($identifier)
    {
        $this->client_id = $identifier;

        return $this;
    }
}