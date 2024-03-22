<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessEmailQueue extends Calls
{
    public $funcName = 'Process Email Queue';

    protected $args;

    public function run(array $args = [])
    {
        $thisCall = $this;

        return function() use ($thisCall, $args) {
            $thisCall->updateJobTask(2, $args);

            $this->args = $this->extractCallArgs($thisCall, $args);

            if (!$this->args) {
                return;
            }

            if (!isset($this->args['priority'])) {
                $thisCall->packagesData->responseCode = 1;

                if (!isset($this->args['priority'])) {
                    $thisCall->packagesData->responseMessage = 'Parameters priority missing';
                }

                $this->addJobResult($thisCall->packagesData, $args);

                $thisCall->updateJobTask(3, $args);

                return;
            }

            $this->basepackages->emailqueue->processQueue((int) $this->args['priority']);

            $this->addJobResult($this->basepackages->emailqueue->packagesData, $args);

            $thisCall->updateJobTask(3, $args);
        };
    }
}