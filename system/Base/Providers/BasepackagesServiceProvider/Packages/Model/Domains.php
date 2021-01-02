<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class Domains extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $default_application_id;

    public $applications;

    public $settings;
}