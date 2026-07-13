<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use System\Base\BaseModel;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;

class ServiceProviderApi extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $status;

    public $api_type;

    public $name;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $description;

    public $registration_allowed;

    public $client_keys_generation_allowed;

    public $client_id_length;

    public $client_secret_length;

    public $grant_type;

    public $cc_max_devices;

    public $client_id;

    public $csrf;

    public $authorization_tos_pp;

    public $state;

    public $private_key;

    public $encryption_key_size;

    public $pki_key_size;

    public $pki_algorithm;

    public $private_key_passphrase;

    public $private_key_location;

    public $access_token_timeout;

    public $refresh_token_timeout;

    public $scope_id;

    public $concurrent_calls_limit;

    public $per_minute_calls_limit;

    public $per_hour_calls_limit;

    public $per_day_calls_limit;

    public $openapi_name;

    public $openapi_description;

    public $openapi_email;

    public $openapi_license_name;

    public $openapi_license_url;

    public $openapi_server_sandbox_url;

    public $openapi_server_sandbox_description;

    public $openapi_server_production_url;

    public $openapi_server_production_description;

    public function initialize()
    {
        $this->modelRelations['scope']['relationObj'] = $this->hasOne(
            'scope_id',
            ServiceProviderApiScopes::class,
            'id',
            [
                'alias'         => 'scope'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}