<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class Storages extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $type;

    public $settings;
}