<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
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

    public function addJob(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added job', 0, null, true);

            return true;
        } else {
            $this->addResponse('Error adding job', 1);

            return false;
        }
    }

    public function updateJob(array $data)
    {
        $job = $this->getById($data['id']);

        $job = array_merge($job, $data);

        if ($this->update($job)) {
            $this->addResponse('Updated job ID ' . $job['id']);
        } else {
            $this->addResponse('Error updating job', 1);
        }
    }
}