<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Library\OAuthHelper;
use System\Base\Providers\ApiServiceProvider\Library\Utils;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAccessTokens;

class AccessTokenRepository extends BasePackage implements AccessTokenRepositoryInterface
{
    use Utils, OAuthHelper;

    public function modelName()
    {
        return ServiceProviderApiAccessTokens::class;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessToken();
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        $accessToken->setUserIdentifier($userIdentifier);
        $accessToken->setClient($clientEntity);
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $accessToken = $accessTokenEntity->getIdentifier();
        if ($this->findOne(['access_token' => $accessToken])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->create([
            'access_token' => $accessToken,
            'expires' => $this->formatDateTime($accessTokenEntity->getExpiryDateTime()),
            'scope' => implode(SCOPE_DELIMITER_STRING, $this->getScopeNamesFromAccessToken($accessTokenEntity)),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'revoked' => 0
        ]);
    }

    public function revokeAccessToken($tokenId)
    {
        $this->update(['access_token' => $tokenId], ['revoked' => 1]);
    }

    public function isAccessTokenRevoked($tokenId)
    {
        if ($result = $this->findOne(['access_token' => $tokenId])) {
            return (int)$result->revoked === 1;
        }

        return true;
    }
}