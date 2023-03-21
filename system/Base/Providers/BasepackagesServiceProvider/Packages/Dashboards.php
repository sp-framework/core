<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;

class Dashboards extends BasePackage
{
    protected $modelToUse = BasepackagesDashboards::class;

    public $dashboards;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function getDashboardById(int $id, $getwidgets = true)
    {
        $this->getFirst('id', $id);

        if ($this->model) {
            $dashboard = $this->model->toArray();

            if ($getwidgets) {
                if ($this->model->getwidgets()) {
                    $relationData = $this->model->getwidgets()->toArray();

                    unset($relationData['id']);

                    $dashboard = array_merge($dashboard, $relationData);
                }
            }

            return $dashboard;
        }

        return false;
    }
}