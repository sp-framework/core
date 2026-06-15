<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;

class WorkersTask extends Task
{
    public function mainAction()
    {
        echo "you hit workers task main action, nothing to do\n";
    }

    public function runAction()
    {
        $initWorkers = $this->basepackages->workers->init();

        if ($initWorkers) {
            try {
                $initWorkers->run();
            } catch (\throwable $e) {
                trace([$e]);
            }
        }
    }

    public function execAction($taskId, $jobId)
    {
        try {
            $this->basepackages->workers->exec($taskId, $jobId);
        } catch (\throwable $e) {
            trace([$e]);
        }
    }
}