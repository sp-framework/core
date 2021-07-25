<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\BasePackage;

class EmailQueueMediumPriority extends BasePackage
{
    public $funcName = 'Email Queue (Medium Priority)';

    public function run(array $args = [])
    {
        return function() use ($args) {
            echo 'Medium';
        };
    }
}