<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use System\Base\BaseModel;

class ServiceProviderApiScopes extends BaseModel implements ScopeEntityInterface
{
    public $id;

    public $app_id;

    public $domain_id;

    public $scope;

    public $is_default;

    public $created_at;

    public $updated_at;

    public function getIdentifier()
    {
        return $this->scope;
    }

    public function setIdentifier($identifier)
    {
        $this->scope = $identifier;

        return $this;
    }
}
