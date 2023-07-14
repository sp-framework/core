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

            $this->addJobResult($result, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}