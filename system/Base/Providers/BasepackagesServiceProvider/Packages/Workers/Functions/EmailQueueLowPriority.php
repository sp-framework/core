<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class EmailQueueLowPriority extends Functions
{
    public $funcName = 'Email Queue (Low Priority)';

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $this->basepackages->emailqueue->processQueue(3);

            $this->addJobResult($this->basepackages->emailqueue->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}