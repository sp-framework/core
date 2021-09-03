<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersJobs extends BaseModel
{
    public $id;

    public $task_id;

    public $worker_id;

    public $run_on;

    public $status;

    public $type;

    public $execution_time;

    public $response_code;

    public $response_message;

    public $response_data;
}