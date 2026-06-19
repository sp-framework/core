<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessEmailQueue extends Calls
{
    public $funcDisplayName = 'Process Email Queue';

    public $funcDescription = 'Process email queue with this call.';

    protected $args;

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->args = $this->extractCallArgs($this, $args);

        if (!$this->args) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function arguments missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        if (!isset($this->args['priority'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function argument "priority" missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        if (isset($this->args['confidential']) && $this->args['confidential'] == 'true') {
            $this->basepackages->emailqueue->processQueue((int) $this->args['priority'], true);
        } else {
            $this->basepackages->emailqueue->processQueue((int) $this->args['priority']);
        }

        $this->addJobResult($this->basepackages->emailqueue->packagesData, $args);

        $this->updateJobTask(3, $args);
    }
}