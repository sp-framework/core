<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Core extends BaseModel
{
    public $id;

    public $name;

    public $display_name;

    public $description;

    public $version;

    public $repo;

    public $settings;

    public $files;

    public $update_available;

    public $update_version;
}