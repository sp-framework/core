<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Library\OAuthHelper;
use System\Base\Providers\ApiServiceProvider\Library\Utils;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiRefreshTokens;

class RefreshTokenRepository extends BasePackage implements RefreshTokenRepositoryInterface
{
    use Utils, OAuthHelper;

    public function modelName()
    {
        return ServiceProviderApiRefreshTokens::class;
    }

    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $token = $refreshTokenEntity->getIdentifier();
        if ($this->findOne(['refresh_token' => $token])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $accessToken = $refreshTokenEntity->getAccessToken();
        $this->create([
            'refresh_token' => $token,
            'expires' => $this->formatDateTime($refreshTokenEntity->getExpiryDateTime()),
            'client_id' => $accessToken->getClient()->getIdentifier(),
            'user_id' => $accessToken->getUserIdentifier(),
            'revoked' => 0,
        ]);
    }

    public function revokeRefreshToken($tokenId)
    {
        $this->update(['refresh_token' => $tokenId], ['revoked' => 1]);
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        if ($result = $this->findOne(['refresh_token' => $tokenId])) {
            return (int)$result->revoked === 1;
        }

        return true;
    }
}
