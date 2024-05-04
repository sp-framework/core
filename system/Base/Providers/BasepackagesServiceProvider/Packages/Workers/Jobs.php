<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Carbon\Carbon;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\BasepackagesApiClientServicesCalls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersJobs;

class Jobs extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersJobs::class;

    protected $packageName = 'jobs';

    public $jobs;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function getJobById($id)
    {
        $job = $this->getById($id);

        if (!$job) {
            return false;
        }

        if (is_array($job['response_code']) && count($job['response_code']) === 1) {
            $job['response_code'] = $job['response_code'][array_key_first($job['response_code'])];
        } else if (is_array($job['response_code']) && count($job['response_code']) > 1) {
            $job['response_code'] = $this->helper->encode($job['response_code']);
        }

        if (is_array($job['response_message']) && count($job['response_message']) === 1) {
            $job['response_message'] = $job['response_message'][array_key_first($job['response_message'])];
        } else if (is_array($job['response_message']) && count($job['response_message']) > 1) {
            $job['response_message'] = $this->helper->encode($job['response_message']);
        }

        if (is_array($job['response_data'])) {
            $job['response_data'] = $this->helper->encode($job['response_data']);
        }

        $apiModel = BasepackagesApiClientServicesCalls::class;

        if (is_string($job['run_on'])) {
            $job['run_on'] = $this->helper->decode($job['run_on'], true);
        }

        if (isset($job['run_on'][0]) && $job['run_on'][0] !== '' && $job['run_on'][0] != '0' && $job['run_on'][0] !== '-') {
            $start = $job['run_on'][0];
            $timeRan = Carbon::createFromFormat('Y-m-d H:i:s', $start);
            $timeRan->addSeconds((float) round($job['execution_time'] ?? 0));
            $end = $timeRan->format('Y-m-d H:i:s');

            if ($this->config->databasetype === 'db') {
                $callsObj = $apiModel::find(
                    [
                        'conditions'        => 'called_at BETWEEN :start: AND :end:',
                        'bind'              =>
                            [
                                'start'     => $start,
                                'end'       => $end
                            ]
                    ]
                );
                $callsArr = $callsObj->toArray();
            } else {
                $apiStore = $this->ff->store((new $apiModel)->getSource());

                $callsArr = $apiStore->findBy(['called_at', 'BETWEEN', [$start, $end]]);
            }

            if ($callsArr && count($callsArr) > 0) {
                $calls = [];

                foreach ($callsArr as $key => $call) {
                    $calls[$call['id']] = $call;
                }

                $job['calls'] = $calls;
            }
        }

        return $job;
    }

    public function addJob(array $data)
    {
        if ($this->add($data, false)) {
            $this->addResponse('Added job', 0, null, true);

            return true;
        } else {
            $this->addResponse('Error adding job', 1);

            return false;
        }
    }

    public function updateJob(array $data)
    {
        $job = $this->getById($data['id'], false, false);

        $job = array_merge($job, $data);

        if ($this->update($job)) {
            $this->addResponse('Updated job ID ' . $job['id']);
        } else {
            $this->addResponse('Error updating job', 1);
        }
    }
}