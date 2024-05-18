<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;

class TerminalTask extends Task
{
    public function mainAction()
    {
        $this->runAction();
    }

    public function runAction()
    {
        $this->terminal->run();
    }
}