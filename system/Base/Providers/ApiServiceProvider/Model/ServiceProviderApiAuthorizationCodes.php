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

    public $id;

    public $app_id;

    public $domain_id;

    public $authorization_code;

    public $expires;

    public $redirect_url;

    public $scope;

    public $client_id;

    public $account_id;

    public $revoked;

    public $created_at;

    public $updated_at;
}
