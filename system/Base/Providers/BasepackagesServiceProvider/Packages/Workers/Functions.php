<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Functions extends BasePackage
{
    protected $startTime;

    protected $stopTime;

    protected function updateJobTask($status, $args)
    {
        $this->updateJob($status, $args);

        $this->updateTask($status, $args);
    }

    protected function updateJob($status, $args)
    {
        if ($args['job']) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id']);

            if ($job['status'] != 4) {
                $job['status'] = $status;
            }

            if ($status == 2) {
                $this->startTime = microtime(true);
                $job['run_on'] = date('Y-m-d H:i:s');
            } else if ($status == 3) {
                $this->stopTime = microtime(true);

                $job['execution_time'] = round($this->stopTime - $this->startTime, 3);
            }

            $this->basepackages->workers->jobs->update($job);
        }
    }

    protected function updateTask($status, $args)
    {
        if ($args['task']) {
            $task = $this->basepackages->workers->tasks->getById($args['task']['id']);

            if ($status == 2) {
                $task['status'] = 2;
            } else if ($status == 3) {
                $task['status'] = 1;
                $job = $this->basepackages->workers->jobs->getById($args['job']['id']);

                $task['previous_run'] = $job['run_on'];
            }

            $task['via_job'] = 1;

            $this->basepackages->workers->tasks->updateTask($task);
        }
    }

    protected function addJobResult($packagesData, $args)
    {
        if ($args['job']) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id']);

            $job['result'] = '';

            if (isset($packagesData->responseCode)) {
                $job['result'] .= 'Code: ' . $packagesData->responseCode . '<br>';
                if ($packagesData->responseCode != 0) {
                    $job['status'] = 4;
                }
            }

            if (isset($packagesData->responseMessage)) {
                $job['result'] .= 'Message: ' . $packagesData->responseMessage . '<br>';
            }

            if (isset($packagesData->responseData)) {
                $job['result'] .= 'Data: ' . Json::encode($packagesData->responseData) . '<br>';
            }

            $this->basepackages->workers->jobs->update($job);
        }
    }
}