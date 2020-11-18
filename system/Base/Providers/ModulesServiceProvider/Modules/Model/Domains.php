<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Domains extends BaseModel
{
    public $id;

    public $domain;

    public $description;

    public $settings;
}