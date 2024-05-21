<?php

namespace System\Base\Providers\ModulesServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderModulesQueues extends BaseModel
{
    public $id;

    public $status;

    public $prechecked_at;

    public $prechecked_by;

    public $processed_at;

    public $processed_by;

    public $results;

    public $tasks;

    public $tasks_count;

    public $total;
}