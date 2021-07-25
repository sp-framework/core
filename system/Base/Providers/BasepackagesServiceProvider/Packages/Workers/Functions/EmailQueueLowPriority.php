<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\BasePackage;

class EmailQueueLowPriority extends BasePackage
{
    public $funcName = 'Email Queue (Low Priority)';

    public function run(array $args = [])
    {
        return function() use ($args) {
            echo 'Low';
        };
    }
}