<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards\BasepackagesDashboardsWidgets;

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
                    $dashboard['widgets'] = $this->model->getwidgets()->toArray();
                }
            }

            return $dashboard;
        }

        return false;
    }

    public function addWidgetToDashboard(array $data)
    {
        $dashboardWidgets = $this->useModel(BasepackagesDashboardsWidgets::class);

        $dashboardWidgets->assign($this->jsonData($data));

        try {
            if ($dashboardWidgets->create()) {
                $newWidget = $dashboardWidgets->toArray();

                $newWidget['content'] = $this->basepackages->widgets->getWidget($newWidget['widget_id'], 'content');

                $this->addResponse('Widget added to dashboard.', 0, $newWidget);
            } else {
                $this->addResponse('Could not add widget to dashboard.', 1);
            }
        } catch (\Exception $e) {
            var_dump($e);die();
            $this->addResponse('Could not add widget to dashboard.', 1);
        }
    }
}