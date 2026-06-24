<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessHelloWorld extends Calls
{
    public $funcDisplayName = 'Process Hello World!';

    public $funcDescription = 'SP/User says Hello World!';

    protected $args;

    protected $raw_args;

    protected $raw_cwd;

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->args = $this->extractCallArgs($this, $args);

        if (!$this->args) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function arguments missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        if (!isset($this->args['user'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function argument "user" missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        if ($this->args['user'] === '') {
            $this->packagesData->responseMessage = 'SP says Hello World!';
        } else {
            $this->packagesData->responseMessage = $this->args['user'] . ' says Hello World!';
        }

        $this->packagesData->responseCode = 0;

        $this->addJobResult($this->packagesData, $args);

        $this->updateJobTask(3, $args);
    }

    public function getRawCmd()
    {
        return $this->raw_cwd = 'echo "Hello World!"';
    }

    /*
    * If we want to terminate a running job, we can only do it if terminate method is available in the call.
    * Calls will register that the job can be terminated via system/workers/jobs/terminate route by adding a can_terminate flag in the DB.
    * Must return true to update job pid to null
    */
    public function terminate($task, $job)
    {
        //Do whatever you want to do at the time of termination.
        $args = ['task' => $task, 'job' => $job];

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Process terminated by : ' . $this->access->auth->account()['email'];

        $this->addJobResult($this->packagesData, $args);

        $this->updateJobTask(3, $args);

        exec("kill -9 " . $job['pid'], $output, $result);

        if ($result === 0) {
            return true;
        }

        return false;
    }
}