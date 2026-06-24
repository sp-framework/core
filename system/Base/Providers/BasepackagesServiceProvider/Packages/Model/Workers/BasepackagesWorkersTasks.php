<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersCalls;

class BasepackagesWorkersTasks extends BaseModel
{
    protected $modelRelations = [];

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

    public $cid;

    public $call_args;

    public $previous_run;

    public $next_run;

    public $force_next_run;

    public $job_log_mode;

    public $email_service_id;

    public $email;

    public $result;

    public function initialize()
    {
        $this->modelRelations['call']['relationObj'] = $this->hasOne(
            'cid',
            BasepackagesWorkersCalls::class,
            'id',
            [
                'alias'         => 'call'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}