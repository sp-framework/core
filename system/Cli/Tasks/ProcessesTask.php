<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;

class ProcessesTask extends Task
{
    protected $processes;

    public function initialize()
    {
        $this->processes = $this->container->getShared('processes');
    }

    public function mainAction()
    {
        echo "you hit processes task main action, nothing to do\n";
    }

    public function runAction()
    {
        $this->processes->run();
    }
}