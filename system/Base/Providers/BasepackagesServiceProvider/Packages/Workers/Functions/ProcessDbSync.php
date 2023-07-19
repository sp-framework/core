<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Carbon\Carbon;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class ProcessDbSync extends Functions
{
    public $funcName = 'Process DB Sync (Hybrid mode)';

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $result['syncRequest'] = $this->ff->getSyncFile();

            $result['result'] = $this->ff->sync();

            $reSync = true;

            if ($result['result'] && isset($result['result']['errors']) && count($result['result']['errors']) === 0) {
                $thisFunction->packagesData->responseCode = 0;

                $thisFunction->packagesData->responseMessage = 'Sync Complete';

                $thisFunction->packagesData->responseData = $result;

                $reSync = false;
            } else {
                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = 'Error Syncing.';

                $thisFunction->packagesData->responseData = $result;

                //Notify the Admins here
            }

            $this->addJobResult($thisFunction->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);

            if (!$reSync) {
                $this->ff->setSync(false);

                $task = $this->basepackages->workers->tasks->findByFunction('processdbsync');

                $time = Carbon::now();

                $task['previous_run'] = $time->format('Y-m-d H:i:s');
                $task['cancel'] = 'true';

                $this->basepackages->workers->tasks->forceNextRun($task);

                $this->ff->resetSync();
            }
        };
    }
}