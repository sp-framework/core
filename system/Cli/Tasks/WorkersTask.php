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
        $this->basepackages->workers->run();
    }

    public function execAction($taskId, $jobId)
    {
        $this->basepackages->workers->exec($taskId, $jobId);
    }
}