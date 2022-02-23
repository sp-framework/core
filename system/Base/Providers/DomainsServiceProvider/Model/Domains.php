<?php

namespace System\Base\Providers\DomainsServiceProvider\Model;

use System\Base\BaseModel;

class Domains extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $default_app_id;

    public $exclusive_to_default_app;

    public $apps;

    public $settings;
}