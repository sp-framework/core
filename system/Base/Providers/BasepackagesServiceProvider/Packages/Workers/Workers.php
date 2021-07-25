<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersWorkers;

class Workers extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersWorkers::class;

    protected $packageName = 'workers';

    public $workers;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function updateWorker(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated worker ' . $data['name']);
        } else {
            $this->addResponse('Error updating worker', 1);
        }
    }

    public function getIdleWorkers()
    {
        $filter =
            $this->model->filter(
                function($function) {
                    $function = $function->toArray();

                    if ($function['status'] == 0 &&
                        $function['enabled'] == 1
                    ) {
                        return $function;
                    }
                }
            );

        return $filter;
    }
}