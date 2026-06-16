<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use Carbon\Carbon;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessRepoSync extends Calls
{
    public $funcDisplayName = 'Process Repo Sync';

    public $funcDescription = 'Process sync of a repository via API.';

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->args = $this->extractCallArgs($this, $args);

        if (!$this->args) {
            return;
        }

        if (!isset($this->args['api_id'])) {
            $this->packagesData->responseCode = 1;

            if (!isset($this->args['api_id'])) {
                $this->packagesData->responseMessage = 'Parameters api_id missing';
            }

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        try {
            if ($this->modules->manager->syncRemoteWithLocal(['api_id' => $this->args['api_id'], 'get_repository_modules' => true])) {
                $counter = $this->modules->manager->packagesData->counter;

                $this->packagesData->responseCode = $this->modules->manager->packagesData->responseCode;

                $this->packagesData->responseMessage = $this->modules->manager->packagesData->responseMessage;

                $this->packagesData->responseData = ['counter' => $counter, 'calls' => $this->basepackages->apiClientServices->usedApi->getWebCalls()];

                //Notify Admins here.
            } else {
                $this->packagesData->responseCode = $this->modules->manager->packagesData->responseCode;

                $this->packagesData->responseMessage = $this->modules->manager->packagesData->responseMessage;

                $this->packagesData->responseData = [];
            }
        } catch (\throwable $e) {
            $this->packagesData->responseCode = '1';

            $this->packagesData->responseMessage = $e->getMessage();

            $this->packagesData->responseData = [];

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        $this->addJobResult($this->packagesData, $args);

        $this->updateJobTask(3, $args);
    }
}