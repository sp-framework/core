<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Library\OAuthHelper;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAuthorizationCodes;

class AuthCodeRepository extends BasePackage implements AuthCodeRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiAuthorizationCodes::class;

    protected $code;

    public function getNewAuthCode()
    {
        return new $this->$modelToUse();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCode = $authCodeEntity->getIdentifier();
        if ($this->findOne(['authorization_code' => $authCode])) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->create([
            'authorization_code' => $authCode,
            'expires' => (\Carbon\Carbon::parse($authCodeEntity->getExpiryDateTime()))->toDateTimeLocalString(),
            'scope' => implode(SCOPE_DELIMITER_STRING, $this->getScopeNamesFromAuthCode($authCodeEntity)),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            // I do not understand why redirect_uri isn't saving to the oauth_codes table.. Must be witchcraft
            // switching to redirect_url
            //'redirect_uri' => $authCodeEntity->getRedirectUri(),
            'redirect_url' => $authCodeEntity->getRedirectUri(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'revoked' => 0,
        ]);
    }

    public function revokeAuthCode($codeId)
    {
        $this->update(['authorization_code' => $codeId, 'revoked' => 1]);
    }

    public function isAuthCodeRevoked($codeId)
    {
        if ($result = $this->getFirst('authorization_code', $codeId)) {
            return (int)$result->revoked === 1;
        }

        return true;
    }
}