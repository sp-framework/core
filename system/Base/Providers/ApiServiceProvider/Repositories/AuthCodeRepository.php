<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Library\OAuthHelper;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAuthorizationCodes;

class AuthCodeRepository extends BasePackage implements AuthCodeRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiAuthorizationCodes::class;

    protected $code;

    public function getNewAuthCode() :ServiceProviderApiAuthorizationCodes
    {
        return $this->useModel();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) :void
    {
        $authCode = $authCodeEntity->getIdentifier();

        if ($this->getFirst('authorization_code', $authCode)) {
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
                        'accountId'=> (int) $authCodeEntity->getClient()->getUserIdentifier()
                    ]
            ];
        } else {
            $params['conditions'] = [
                ['api_id', '=', (int) $this->api->getApiInfo()['id']],
                ['app_id', '=', (int) $this->apps->getAppInfo()['id']],
                ['domain_id', '=', (int) $this->domains->domain['id']],
                ['account_id', '=', (int) $authCodeEntity->getClient()->getUserIdentifier()]
            ];
        }
        $code = $this->getByParams($params, false, false);

        $newCode =
            [
                'api_id' => (int) $this->api->getApiInfo()['id'],
                'app_id' => (int) $this->apps->getAppInfo()['id'],
                'domain_id' => (int) $this->domains->domain['id'],
                'account_id' => (int) $authCodeEntity->getClient()->getUserIdentifier(),
                'authorization_code' => $authCode,
                'expires' => (\Carbon\Carbon::parse($authCodeEntity->getExpiryDateTime()))->toDateTimeLocalString(),
                'client_id' => $authCodeEntity->getClient()->getIdentifier(),
                'revoked' => 0,
                'redirectUri' => $authCodeEntity->getClient()->getRedirectUri()
            ];

        if (!$code) {
            $this->add($newCode);
        } else {
            if (count($code) > 0) {
                $code = $code[0];//We only change the first code found.
            }

            $newCode = array_merge($code, $newCode);

            $this->update($newCode);
        }

        $authCodeEntity->assign($newCode);
    }

    public function revokeAuthCode($codeId) :void
    {
        if ($result = $this->getFirst('authorization_code', $codeId)) {
            $result = $result->toArray();

            $result['revoked'] = true;

            $this->update($result);
        }
    }

    public function isAuthCodeRevoked($codeId) :bool
    {
        if ($result = $this->getFirst('authorization_code', $codeId)) {
            return (int)$result->revoked === 1;
        }

        return true;
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