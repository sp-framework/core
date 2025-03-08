<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersCalls extends BaseModel
{
    public $id;

    public $name;

    public $display_name;

    public $description;

    public $class;

    public $can_be_scheduled;

    public $can_be_run_on_demand;

    public $package_id;
}