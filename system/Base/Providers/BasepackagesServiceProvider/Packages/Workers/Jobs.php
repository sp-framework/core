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
            $this->addResponse('Added job');
        } else {
            $this->addResponse('Error adding job', 1);
        }
    }

    public function updateJob(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated job ID ' . $data['id']);
        } else {
            $this->addResponse('Error updating job', 1);
        }
    }
}