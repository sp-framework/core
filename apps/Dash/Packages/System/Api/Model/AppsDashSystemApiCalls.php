<?php

namespace Apps\Dash\Packages\System\Api\Model;

use System\Base\BaseModel;

class AppsDashSystemApiCalls extends BaseModel
{
    public $id;

    public $api_id;

    public $called_at;

    public $call_method;

    public $call_response_code;

    public $call_exec_time;

    public $call_stats;

    public $call_error;
}