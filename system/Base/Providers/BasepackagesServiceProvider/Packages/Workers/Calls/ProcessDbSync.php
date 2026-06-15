<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use Carbon\Carbon;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessDbSync extends Calls
{
    public $funcDisplayName = 'Process DB Sync (Hybrid mode)';

    public $funcDescription = 'Process sync of FF data with DB data.';

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $result['syncRequest'] = $this->ff->getSyncFile();

        $result['result'] = $this->ff->sync();

        $reSync = true;
        if ($result['result'] && isset($result['result']['errors']) && count($result['result']['errors']) === 0) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Sync Complete';

            $this->packagesData->responseData = $result;

            $reSync = false;
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Syncing.';

            $this->packagesData->responseData = $result;

            //Notify the Admins here
        }

        $this->addJobResult($this->packagesData, $args);

        $this->updateJobTask(3, $args);

        if (!$reSync) {
            $this->ff->setSync(false);

            $task = $this->basepackages->workers->tasks->findByCall('processdbsync');

            $time = Carbon::now();

            $task['previous_run'] = $time->format('Y-m-d H:i:s');
            $task['cancel'] = 'true';

            $this->basepackages->workers->tasks->forceNextRun($task);

            $this->ff->resetSync();
        }
    }
}