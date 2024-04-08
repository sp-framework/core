<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiRefreshTokens;

class RefreshTokenRepository extends BasePackage implements RefreshTokenRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiRefreshTokens::class;

    protected $token;

    public function getNewRefreshToken()
    {
        return new ServiceProviderApiRefreshTokens();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshToken = $refreshTokenEntity->getIdentifier();

        if ($this->getFirst('refresh_token', $refreshToken)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $accessToken = $refreshTokenEntity->getAccessToken();

        if ($this->config->databasetype === 'db') {
            $params = [
                'conditions'    => 'app_id = :appId: AND domain_id = :domainId: AND account_id = :accountId:',
                'bind'          =>
                    [
                        'appId'    => $this->apps->getAppInfo()['id'],
                        'domainId' => $this->domains->domain['id'],
                        'accountId'=> $accessToken->getUserIdentifier()
                    ]
            ];
        } else {
            $params['conditions'] = [
                ['app_id', '=', $this->apps->getAppInfo()['id']],
                ['domain_id', '=', $this->domains->domain['id']],
                ['account_id', '=', $accessToken->getUserIdentifier()]
            ];
        }

        $token = $this->getByParams($params, false, false);

        $newToken = [
            'app_id' => $this->apps->getAppInfo()['id'],
            'domain_id' => $this->domains->domain['id'],
            'account_id' => $accessToken->getUserIdentifier(),
            'refresh_token' => $refreshToken,
            'expires' => (\Carbon\Carbon::parse($accessToken->getExpiryDateTime()))->toDateTimeLocalString(),
            'client_id' => $accessToken->getClient()->getIdentifier(),
            'revoked' => 0,
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

    public function revokeRefreshToken($tokenId)
    {
        $this->update(['refresh_token' => $tokenId], ['revoked' => 1]);
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        if ($result = $this->getFirst('refresh_token', $tokenId)) {
            return (int)$result->revoked === 1;
        }

        return true;
    }
}
