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
        try {
            $this->basepackages->workers->run();
        } catch (\Exception $e) {
            var_dump($e);die();
        }
    }

    public function execAction($taskId, $jobId)
    {
        $this->basepackages->workers->exec($taskId, $jobId);
    }
}