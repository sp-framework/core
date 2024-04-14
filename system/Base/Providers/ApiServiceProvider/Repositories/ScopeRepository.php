<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApi;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;

class ScopeRepository extends BasePackage implements ScopeRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiScopes::class;

    protected $scope;

    public function getScopeEntityByIdentifier($identifier)
    {
        $scopeObj = $this->getFirst('scope_name', $identifier);

        if ($scopeObj) {
            $this->scope = $scopeObj->toArray();

            $scopeObj = new $this->modelToUse;

            $scopeObj->assign($this->scope);

            return $scopeObj;
        }

        return false;
    }

    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        $result = [];

        $this->modelToUse = ServiceProviderApiClients::class;
        $this->setFfStoreToUse();

        $clientObj = $this->getFirst('client_id', $clientEntity->getIdentifier());

        if ($clientObj) {
            $this->modelToUse = ServiceProviderApi::class;
            $this->setFfStoreToUse();

            $api = $this->getById($clientObj->api_id);

            if ($api) {
                $this->modelToUse = ServiceProviderApiScopes::class;
                $this->setFfStoreToUse();

                $scope = $this->getById($api['scope_id']);

                if ($scope) {
                    $result[] = $this->getScopeEntityByIdentifier($scope['scope_name']);
                }
            }
        }

        return $result;
    }
}