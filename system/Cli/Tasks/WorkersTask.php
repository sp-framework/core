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
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->critical(json_trace($e));
                }

                throw $e;
            }
        }
    }

    public function execAction($taskKeyword, $taskId, $jobKeyword, $jobId)
    {
        if ($this->basepackages->workers) {
            try {
                $this->basepackages->workers->exec($taskId, $jobId);
            } catch (\throwable $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->critical(json_trace($e));
                }

                throw $e;
            }
        }
    }
}