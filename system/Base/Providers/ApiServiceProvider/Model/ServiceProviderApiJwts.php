<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderApiJwts extends BaseModel
{
    public $id;

    public $api_id;

    public $app_id;

    public $domain_id;

    public $client_id;

    public $subject;

    public $public_key;
}