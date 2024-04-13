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
                'conditions'    => 'api_id = :apiId: AND app_id = :appId: AND domain_id = :domainId: AND account_id = :accountId:',
                'bind'          =>
                    [
                        'apiId'    => $this->api->getApiInfo()['id'],
                        'appId'    => $this->apps->getAppInfo()['id'],
                        'domainId' => $this->domains->domain['id'],
                        'accountId'=> $accessToken->getClient()->getUserIdentifier()
                    ]
            ];
        } else {
            $params['conditions'] = [
                ['api_id', '=', $this->api->getApiInfo()['id']],
                ['app_id', '=', $this->apps->getAppInfo()['id']],
                ['domain_id', '=', $this->domains->domain['id']],
                ['account_id', '=', $accessToken->getClient()->getUserIdentifier()]
            ];
        }

        $token = $this->getByParams($params, false, false);

        $newToken = [
            'api_id' => $this->api->getApiInfo()['id'],
            'app_id' => $this->apps->getAppInfo()['id'],
            'domain_id' => $this->domains->domain['id'],
            'account_id' => $accessToken->getClient()->getUserIdentifier(),
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
        if ($result = $this->getFirst('refresh_token', $tokenId)) {

            $result = $result->toArray();

            $result['revoked'] = true;

            $this->update($result);
        }
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        if ($result = $this->getFirst('refresh_token', $tokenId)) {
            return (int)$result->revoked === 1;
        }

        return true;
    }
}
