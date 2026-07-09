<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use Carbon\Carbon;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Library\OAuthHelper;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAccessTokens;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;

class AccessTokenRepository extends BasePackage implements AccessTokenRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiAccessTokens::class;

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null) :ServiceProviderApiAccessTokens
    {
        $accessToken = $this->useModel();

        foreach ($scopes as $scope) {
            if ($scope instanceof ServiceProviderApiScopes) {
                $accessToken->addScope($scope->scope_name);
            } else {
                $accessToken->addScope($scope);
            }
        }

        $accessToken->setUserIdentifier($userIdentifier);
        $accessToken->setClient($clientEntity);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) :void
    {
        $accessToken = $accessTokenEntity->getIdentifier();

        if ($this->getFirst('access_token', $accessToken)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        if ($this->config->databasetype === 'db') {
            $params = [
                'conditions'    => 'api_id = :apiId: AND app_id = :appId: AND domain_id = :domainId: AND account_id = :accountId:',
                'bind'          =>
                    [
                        'apiId'    => (int) $this->api->getApiInfo()['id'],
                        'appId'    => (int) $this->apps->getAppInfo()['id'],
                        'domainId' => (int) $this->domains->domain['id'],
                        'accountId'=> (int) $accessTokenEntity->getClient()->getUserIdentifier()
                    ]
            ];
        } else {
            $params['conditions'] = [
                ['api_id', '=', (int) $this->api->getApiInfo()['id']],
                ['app_id', '=', (int) $this->apps->getAppInfo()['id']],
                ['domain_id', '=', (int) $this->domains->domain['id']],
                ['account_id', '=', (int) $accessTokenEntity->getClient()->getUserIdentifier()]
            ];
        }

        $token = $this->getByParams($params, false, false);

        $newToken = [
            'api_id' => (int) $this->api->getApiInfo()['id'],
            'app_id' => (int) $this->apps->getAppInfo()['id'],
            'domain_id' => (int) $this->domains->domain['id'],
            'account_id' => (int) $accessTokenEntity->getClient()->getUserIdentifier(),
            'access_token' => $accessToken,
            'expires' => (\Carbon\Carbon::parse($accessTokenEntity->getExpiryDateTime()))->toDateTimeLocalString(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'revoked' => 0
        ];

        if (!$token) {
            $this->add($newToken, false);
        } else {
            if (count($token) > 0) {
                $token = $token[0];//We only change the first token found.
            }

            $newToken = array_merge($token, $newToken);

            $this->update($newToken);
        }
    }

    public function revokeAccessToken($tokenId) :void
    {
        if ($result = $this->getFirst('access_token', $tokenId)) {
            $result = $result->toArray();

            $result['revoked'] = true;

            $this->update($result);
        }
    }

    public function isAccessTokenRevoked($tokenId) :bool
    {
        if ($result = $this->getFirst('access_token', $tokenId)) {
            return (int) $result->revoked === 1;
        }

        return true;
    }

    public function isTokenExpired($tokenId)
    {
        if ($result = $this->getFirst('access_token', $tokenId)) {
            $result = $result->toArray();

            if ($result['expires'] === '') {
                return true;
            }

            try {
                $expiry = (new Carbon)->parse($result['expires']);

                if ($expiry->isPast()) {
                    return true;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return false;
    }

    public function getUserFromToken($tokenId)
    {
        if ($result = $this->getFirst('access_token', $tokenId)) {
            $result = $result->toArray();

            if ($result['account_id']) {
                return $this->basepackages->accounts->getAccountById($result['account_id']);
            }
        }

        return false;
    }

    protected function getScopeNamesFromAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        return $this->scopeToArray($accessTokenEntity->getScopes());
    }

    protected function getScopeNamesFromAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        return $this->scopeToArray($authCodeEntity->getScopes());
    }

    protected function scopeToArray(array $scopes)
    {
        $scopeNames = [];
        foreach ($scopes as $scope) {
            $scopeNames[] = $scope->getIdentifier();
        }

        return $scopeNames;
    }
}