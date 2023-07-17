<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

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

            if ($result['result']) {
                $thisFunction->packagesData->responseCode = 0;

                $thisFunction->packagesData->responseMessage = 'Sync Complete';

                $thisFunction->packagesData->responseData = $result;
            } else {
                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = 'Error Syncing.';

                $thisFunction->packagesData->responseData = $result;
            }

            $this->addJobResult($thisFunction->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}