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

    public function getScopeEntityByIdentifier($identifier) :ServiceProviderApiScopes
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
        $userIdentifier = null,
        $authCodeId = null
    ) :array
    {
        $result = [];

        $this->setModelToUse($this->modelToUse = ServiceProviderApiClients::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $clientObj = $this->getFirst('client_id', $clientEntity->getIdentifier());

        if ($clientObj) {
            $this->setModelToUse($this->modelToUse = ServiceProviderApi::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $api = $this->getById($clientObj->api_id);

            if ($api) {
                $this->setModelToUse($this->modelToUse = ServiceProviderApiScopes::class);

                $this->ffStore = $this->ff->store($this->ffStoreToUse);

                $scope = $this->getById($api['scope_id']);

                if ($scope) {
                    $scopeObj = $this->getScopeEntityByIdentifier($scope['scope_name']);

                    $result[] = $scopeObj->scope_name;
                }
            }
        }

        return $result;
    }
}