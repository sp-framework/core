<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\BasePackage;

class EmailQueueHighPriority extends BasePackage
{
    public $funcName = 'Email Queue (High Priority)';

    public function run(array $args = [])
    {
        return function() use ($args) {
            echo 'High';
        };
    }
}