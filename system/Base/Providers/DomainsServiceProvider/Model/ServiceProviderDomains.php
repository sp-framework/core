<?php

namespace System\Base\Providers\DomainsServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderDomains extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $default_app_id;

    public $exclusive_to_default_app;

    public $apps;

    public $dns_record;

    public $is_internal;

    public $settings;
}