<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessHousekeeping extends Calls
{
    public $funcDisplayName = 'Process Housekeeping';

    public $funcDescription = 'Process housekeeping functions via this call.';

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

        if (!isset($this->args['tasks'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function argument "tasks" missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        //Perform tasks here
        try {
            $this->basepackages->houseKeeping->run($this->args['tasks']);

            $this->addJobResult($this->basepackages->houseKeeping->packagesData, $args);

            if ($this->basepackages->houseKeeping->packagesData->responseCode == 0) {
                $this->updateJobTask(3, $args);
            } else {
                $this->updateJobTask(4, $args);
            }
        } catch (\throwable $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }
    }
}