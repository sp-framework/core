<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersTasks extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $function;

    public $schedule_id;

    public $is_external;

    public $is_raw;

    public $priority;

    public $enabled;

    public $status;

    public $type;//System OR User

    public $previous_run;

    public $next_run;

    public $email;

    public $result;
}