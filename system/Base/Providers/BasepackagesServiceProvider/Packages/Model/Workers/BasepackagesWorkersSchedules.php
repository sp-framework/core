<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersSchedules extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $schedule;

    public $type;//System OR User
}