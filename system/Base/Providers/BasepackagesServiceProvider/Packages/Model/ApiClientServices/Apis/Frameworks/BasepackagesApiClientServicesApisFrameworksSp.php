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

    public $request_url;

    public $redirect_uri;

    public $refresh_url;

    public $access_token;

    public $refresh_token;

    public $grant_type;

    public $expires;

    public function init($app = null)
    {
        $this->setTableSource('basepackages_api_client_services_apis_frameworks');

        return $this;
    }
}