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
        $workers = $this->basepackages->workers;

        if ($workers) {
            $workers->run();
        }
    }
}