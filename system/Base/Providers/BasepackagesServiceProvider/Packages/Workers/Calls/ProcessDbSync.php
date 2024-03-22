<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use Carbon\Carbon;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessDbSync extends Calls
{
    public $funcName = 'Process DB Sync (Hybrid mode)';

    public function run(array $args = [])
    {
        $thisCall = $this;

        return function() use ($thisCall, $args) {
            $thisCall->updateJobTask(2, $args);

            $result['syncRequest'] = $this->ff->getSyncFile();

            $result['result'] = $this->ff->sync();

            $reSync = true;

            if ($result['result'] && isset($result['result']['errors']) && count($result['result']['errors']) === 0) {
                $thisCall->packagesData->responseCode = 0;

                $thisCall->packagesData->responseMessage = 'Sync Complete';

                $thisCall->packagesData->responseData = $result;

                $reSync = false;
            } else {
                $thisCall->packagesData->responseCode = 1;

                $thisCall->packagesData->responseMessage = 'Error Syncing.';

                $thisCall->packagesData->responseData = $result;

                //Notify the Admins here
            }

            $this->addJobResult($thisCall->packagesData, $args);

            $thisCall->updateJobTask(3, $args);

            if (!$reSync) {
                $this->ff->setSync(false);

                $task = $this->basepackages->workers->tasks->findByCall('processdbsync');

                $time = Carbon::now();

                $task['previous_run'] = $time->format('Y-m-d H:i:s');
                $task['cancel'] = 'true';

                $this->basepackages->workers->tasks->forceNextRun($task);

                $this->ff->resetSync();
            }
        };
    }
}