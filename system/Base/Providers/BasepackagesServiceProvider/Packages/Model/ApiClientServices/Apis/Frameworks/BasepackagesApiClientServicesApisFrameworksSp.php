<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\Apis\Frameworks;

use System\Base\BaseModel;

class BasepackagesApiClientServicesApisFrameworksSp extends BaseModel
{
    public $id;

    public $api_url;

    public $auth_type;

    public $username;

    public $password;

    public $authorization;

    public $device_id;

    public $client_id;

    public $client_secret;

    public $code;

    public $access_token;

    public $token_type;

    public $expires_in;

    public $refresh_token;

    public function init($app = null)
    {
        $this->setTableSource('basepackages_api_client_services_apis_frameworks');

        return $this;
    }
}