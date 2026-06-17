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
        if ($this->basepackages->workers) {
            try {
                $this->basepackages->workers->run();
            } catch (\throwable $e) {
                throw $e;
            }
        }
    }

    public function execAction($taskId, $jobId)
    {
        if ($this->basepackages->workers) {
            try {
                $this->basepackages->workers->exec($taskId, $jobId);
            } catch (\throwable $e) {
                throw $e;
            }
        }
    }
}