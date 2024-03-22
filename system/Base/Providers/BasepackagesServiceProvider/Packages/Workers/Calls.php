<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use System\Base\BasePackage;

class Calls extends BasePackage
{
    protected $startTime;

    protected $stopTime;

    public function updateJobTask($status, $args)
    {
        $this->updateJob($status, $args);

        $this->updateTask($status, $args);
    }

    protected function updateJob($status, $args)
    {
        if (isset($args['job'])) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id'], false, false);

            if ($job['status'] != 4) {
                $job['status'] = $status;
            }

            if ($status == 2) {
                $this->startTime = microtime(true);
                if ($job['run_on']) {
                    if (is_string($job['run_on'])) {
                        $job['run_on'] = $this->helper->decode($job['run_on'], true);
                    }
                    if (!in_array(date('Y-m-d H:i:s'), $job['run_on'])) {
                        array_push($job['run_on'], date('Y-m-d H:i:s'));
                    }
                } else {
                    $job['run_on'] = [date('Y-m-d H:i:s')];
                }
            } else if ($status == 3) {
                $this->stopTime = microtime(true);

                if ($job['execution_time']) {
                    $job['execution_time'] = round($job['execution_time'] + round($this->stopTime - $this->startTime, 3), 3);
                } else {
                    $job['execution_time'] = round($this->stopTime - $this->startTime, 3);
                }
            }

            $this->basepackages->workers->jobs->update($job, false);
        }
    }

    protected function updateTask($status, $args)
    {
        if (isset($args['task'])) {
            $task = $this->basepackages->workers->tasks->getById($args['task']['id'], false, false);

            if ($status == 2) {
                $task['status'] = 2;
            } else if ($status == 3) {
                $task['status'] = 1;
                $job = $this->basepackages->workers->jobs->getById($args['job']['id'], false, false);

                if (is_string($job['run_on'])) {
                    $job['run_on'] = $this->helper->decode($job['run_on'], true);
                }

                $task['previous_run'] = $job['run_on'][0];
            }

            $task['via_job'] = 1;

            $this->basepackages->workers->tasks->updateTask($task, false);
        }
    }

    public function addJobResult($packagesData, $args)
    {
        if (isset($args['job'])) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id'], false, false);

            if (isset($packagesData->responseCode)) {
                if ($packagesData->responseCode != 0) {
                    $job['status'] = 4;
                }
            }

            if (isset($args['schedule']['type']) &&
                $args['schedule']['type'] === 'everyxseconds'
            ) {
                $responseCode = [];
                $responseMessage = [];
                $responseData = [];

                $jobRunOn = date('Y-m-d H:i:s');

                if (isset($packagesData->responseCode)) {
                    $responseCode[$jobRunOn] = $packagesData->responseCode;
                }

                if ($job['response_code'] && is_string($job['response_code'])) {
                    $job['response_code'] = $this->helper->decode($job['response_code'], true);
                }

                if ($job['response_code']) {
                    $job['response_code'] = $this->helper->encode(array_merge($job['response_code'], $responseCode));
                } else {
                    $job['response_code'] = $this->helper->encode($responseCode);
                }

                if (isset($packagesData->responseMessage)) {
                    $responseMessage[$jobRunOn] = $packagesData->responseMessage;
                }

                if ($job['response_message'] && is_string($job['response_message'])) {
                    $job['response_message'] = $this->helper->decode($job['response_message'], true);
                }

                if ($job['response_message']) {
                    $job['response_message'] = $this->helper->encode(array_merge($job['response_message'], $responseMessage));
                } else {
                    $job['response_message'] = $this->helper->encode($responseMessage);
                }


                if (isset($packagesData->responseData)) {
                    $responseData[$jobRunOn] = $packagesData->responseData;
                }

                if ($job['response_data']) {
                    $job['response_data'] = $this->helper->encode(array_merge($job['response_data'], $responseData));
                } else {
                    $job['response_data'] = $this->helper->encode($responseData);
                }
            } else {
                if (isset($packagesData->responseCode)) {
                    $job['response_code'] = $this->helper->encode([$packagesData->responseCode]);
                }

                if (isset($packagesData->responseMessage)) {
                    $job['response_message'] = $this->helper->encode([$packagesData->responseMessage]);
                }

                if (isset($packagesData->responseData)) {
                    $job['response_data'] = $this->helper->encode($packagesData->responseData);
                }
            }

            $this->basepackages->workers->jobs->update($job, false);
        }
    }

    protected function extractCallArgs($thisCall, $args)
    {
        if (isset($args['task']['call_args']) && is_string($args['task']['call_args']) && $args['task']['call_args'] !== '') {
            try {
                return $this->helper->decode($args['task']['call_args'], true);
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), "json_decode")) {
                    $thisCall->packagesData->responseMessage = 'Task call arguments format is incorrect. Make sure the format is json.';
                } else {
                    $thisCall->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';
                }

                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $thisCall->packagesData->responseCode = 1;

                $this->addJobResult($thisCall->packagesData, $args);

                $thisCall->updateJobTask(3, $args);

                return false;
            }
        } else if (is_array($args['task']['call_args'])) {
            return $args['task']['call_args'];
        }

        return false;
    }
}