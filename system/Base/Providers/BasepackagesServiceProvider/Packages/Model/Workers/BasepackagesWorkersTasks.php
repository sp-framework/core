<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersTasks extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $schedule_id;

    public $priority;

    public $is_on_demand;

    public $enabled;

    public $status;

    public $type;

    public $exec_type;

    public $call;

    public $call_args;

    public $php;

    public $php_args;

    public $raw;

    public $raw_args;

    public $pid;

    public $previous_run;

    public $next_run;

    public $force_next_run;

    public $email;

    public $result;
}