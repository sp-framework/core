<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;

class BasepackagesWorkersJobs extends BaseModel
{
    public $id;

    public $task_id;

    public $worker_id;

    public $cid;

    public $api_call_ids;

    public $run_on;

    public $status;

    public $type;

    public $execution_times;

    public $total_execution_time;

    public $job_log_mode;

    public $job_log_time;

    public $response_code;

    public $response_message;

    public $response_data;
}