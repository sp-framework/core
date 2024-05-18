<?php

namespace System\Base\Providers\ModulesServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderModulesQueues extends BaseModel
{
    public $id;

    public $processed;

    public $processed_at;

    public $analysed;

    public $analysed_at;

    public $analysed_result;

    public $tasks;

    public $tasks_result;

    public $tasks_count;
}