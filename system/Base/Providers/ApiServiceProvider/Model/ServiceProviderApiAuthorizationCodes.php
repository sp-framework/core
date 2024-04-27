<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use System\Base\BaseModel;

class ServiceProviderApiAuthorizationCodes extends BaseModel implements AuthCodeEntityInterface
{
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;

    //Note: name and redirectUri properties defined in AuthCodeTrait
    public $id;

    public $api_id;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $client_id;

    public $authorization_code;

    public $expires;

    public $revoked;

    public function getUserIdentifier()
    {
        return $this->account_id;
    }

    public function setUserIdentifier($identifier)
    {
        $this->account_id = $identifier;
    }
}
