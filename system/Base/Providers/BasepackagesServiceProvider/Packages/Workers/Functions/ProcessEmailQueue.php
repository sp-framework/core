<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class ProcessEmailQueue extends Functions
{
    public $funcName = 'Process Email Queue';

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $this->parameters = $this->extractParameters($thisFunction, $args);

            if (!$this->parameters) {
                return;
            }

            if (!isset($this->parameters['priority'])) {
                $thisFunction->packagesData->responseCode = 1;

                if (!isset($this->parameters['priority'])) {
                    $thisFunction->packagesData->responseMessage = 'Parameters priority missing';
                }

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(3, $args);

                return;
            }

            $this->basepackages->emailqueue->processQueue((int) $this->parameters['priority']);

            $this->addJobResult($this->basepackages->emailqueue->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}