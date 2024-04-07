<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderApiJwts extends BaseModel
{
    public $id;

    public $app_id;

    public $domain_id;

    public $client_id;

    public $subject;

    public $public_key;

    public $created_at;

    public $updated_at;
}