<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;

class ScopeRepository extends BasePackage implements ScopeRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiScopes::class;

    protected $scope;

    public function getScopeEntityByIdentifier($identifier)
    {
        $scope = $this->findOne(['scope' => $identifier]);

        return $scope;
    }

    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        //we will ignore $userIdentifier as we do not plan to give user based scopes but client based scopes only
        // the implementation of this part should totally depend on whoever uses this library
        // no need to remove invalid scopes (scopes that do not exist in the database)
        // because they will be validated by the league library

        $client = (new ClientRepository())->findOne(['client_id' => $clientEntity->getIdentifier()]);
        $clientScopes = empty($clientScope) ? null : $client->scope;

        //if scope was not saved for client or * was saved, ignore and return all scopes
        if (empty($clientScopes) || $clientScopes === '*') {
            //grant all scopes
            return $scopes;
        }

        //scopes of client from database
        $clientScopes = array_map('trim', explode(SCOPE_DELIMITER_STRING, $clientScopes));

        //remove any scope requested but not associated to client
        $result = [];
        foreach ($scopes as $scope) {
            if (!in_array($scope->getIdentifier(), $clientScopes)) {
                continue;
            }

            $result[] = $scope;
        }

        //include scope not requested but associated to client (optional)
        if ($this->getConfig()->oauth->always_include_client_scopes) {
            $includedScopes = array_map(function (Scope $scope) {
                return $scope->getIdentifier();
            }, $result);

            $excludedScopes = array_diff($clientScopes, $includedScopes);
            array_walk($excludedScopes, function ($scopeIdentifier) use (&$result) {
                $result[] = $this->getScopeEntityByIdentifier($scopeIdentifier);
            });
        }

        return $result;
    }
}