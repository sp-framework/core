<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersCalls;

class Calls extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersCalls::class;

    protected $packageName = 'calls';

    public $calls;

    protected $jobRunOn;

    protected $startTime;

    protected $stopTime;

    public function init(bool $resetCache = false)
    {
        $this->setFFRelations(true);

        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('calls', 'core')) {
                $this->calls = $this->opCache->getCache('calls', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('calls', $this->calls, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function getByCallName($name)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'name = :name:',
                    'bind'          =>
                        [
                            'name'  => $name
                        ]
                ];

            $call = $this->getByParams($conditions);
        } else {
            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $call = $this->ffStore->findBy(['name', '=', $name]);
        }

        if ($call && count($call) > 0) {
            return $call[0];
        }

        return false;
    }

    public function addCall(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added new call ' . $data['name']);
        } else {
            $this->addResponse('Error adding new call', 1);
        }
    }

    public function updateCall(array $data)
    {
        $call = $this->getById($data['id']);

        $call = array_merge($call, $data);

        if ($this->update($call)) {
            $this->addResponse('Updated call ' . $call['name']);
        } else {
            $this->addResponse('Error updating call', 1);
        }
    }

    public function removeCall(array $data)
    {
        $call = $this->getById($data['id']);

        if ($this->remove($data['id'])) {
            $this->addResponse('Call removed');
        } else {
            $this->addResponse('Error removing call', 1);
        }
    }

    // Task Statuses
    // 1 - Scheduled
    // 2 - Running
    // 3 - Success
    // 4 - Error
    // 5 - Rescheduled (Due to No Workers)

    // Job Statuses
    // 1 - Scheduled
    // 2 - Running
    // 3 - Success
    // 4 - Error
    // 5 - Warning
    public function updateJobTask($status, &$args)
    {
        $this->updateJob($status, $args);

        $this->updateTask($status, $args);
    }

    protected function updateJob($status, &$args)
    {
        if (!$this->jobRunOn) {
            $this->jobRunOn = date('Y-m-d H:i:s');
        }

        if (isset($args['job'])) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id'], false, false);

            $job['status'] = $status;

            if ($status == 2) {
                $this->startTime = microtime(true);
                if ($job['run_on']) {
                    if (is_string($job['run_on'])) {
                        $job['run_on'] = $this->helper->decode($job['run_on'], true);
                    }
                    if (!in_array($this->jobRunOn, $job['run_on'])) {
                        array_push($job['run_on'], $this->jobRunOn);
                    }
                } else {
                    $job['run_on'] = [$this->jobRunOn];
                }
            } else if ($status == 3) {
                $this->stopTime = microtime(true);

                if (isset($job['execution_times'])) {
                    if (is_string($job['execution_times'])) {
                        $job['execution_times'] = $this->helper->decode($job['execution_times'], true);
                    }
                }

                $job['execution_times'][$this->jobRunOn] = round($this->stopTime - $this->startTime, 3);

                if (isset($job['total_execution_time'])) {
                    $job['total_execution_time'] = round($job['total_execution_time'] + round($this->stopTime - $this->startTime, 3), 3);
                } else {
                    $job['total_execution_time'] = round($this->stopTime - $this->startTime, 3);
                }
            }

            $this->basepackages->workers->jobs->updateJob($job, false);

            $args['job'] = $this->basepackages->workers->jobs->packagesData->last;
        }
    }

    protected function updateTask($status, &$args)
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
            } else {
                $task['status'] = $status;
            }

            if ($task['status'] == 4) {
                $task['enabled'] = false;
            }

            $task['via_job'] = 1;

            $this->basepackages->workers->tasks->updateTask($task, false);

            $args['task'] = $this->basepackages->workers->tasks->packagesData->last;
        }
    }

    public function addJobResult($packagesData, &$args)
    {
        if (isset($args['job'])) {
            $job = $this->basepackages->workers->jobs->getById($args['job']['id'], false, false);

            if (isset($packagesData->responseCode)) {
                if ($packagesData->responseCode != 0) {
                    $job['status'] = 4;
                }
            }

            $merge = false;
            if ($args['task']['job_log_mode'] != '1') {
                $merge = true;
            }

            if (isset($args['schedule']['type']) &&
                $args['schedule']['type'] === 'everyxseconds'
            ) {
                $merge = true;
            }

            if ($merge) {
                $responseCode = [];
                $responseMessage = [];
                $responseData = [];

                $this->jobRunOn = $this->jobRunOn;

                if (isset($packagesData->responseCode)) {
                    $responseCode[$this->jobRunOn] = $packagesData->responseCode;
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
                    $responseMessage[$this->jobRunOn] = $packagesData->responseMessage;
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
                    $responseData[$this->jobRunOn] = $packagesData->responseData;
                }

                if ($job['response_data']) {
                    $job['response_data'] = $this->helper->encode(array_merge($job['response_data'], $responseData));
                } else {
                    $job['response_data'] = $this->helper->encode($responseData);
                }
            } else {
                $job['response_code'] = 0;
                $job['response_message'] = 'Ok';
                $job['response_data'] = $this->helper->encode([]);

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

            $this->basepackages->workers->jobs->updateJob($job, false);

            $args['job'] = $this->basepackages->workers->jobs->packagesData->last;
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
                    $this->logger->logExceptions->critical(json_trace($e));
                }

                $thisCall->packagesData->responseCode = 1;

                $this->addJobResult($thisCall->packagesData, $args);

                $thisCall->updateJobTask(4, $args);

                return false;
            }
        } else if (is_array($args['task']['call_args'])) {
            return $args['task']['call_args'];
        }

        return false;
    }
}