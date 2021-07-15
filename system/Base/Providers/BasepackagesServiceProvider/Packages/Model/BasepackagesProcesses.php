<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesProcesses extends BaseModel
{
    public $id;

    public $status;

    public $priority;

    public $error;
}