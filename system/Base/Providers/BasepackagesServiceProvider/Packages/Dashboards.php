<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
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
            $this->addResponse('Could not add widget to dashboard.', 1);
        }
    }

    public function updateWidgetToDashboard(array $data)
    {
        $this->modelToUse = $this->useModel(BasepackagesDashboardsWidgets::class);

        try {
            foreach ($data['widgets'] as $key => $widget) {
                $dbWidget = $this->getFirst('id', $widget['id']);

                if ($dbWidget) {
                    unset($widget['id']);

                    $dbWidget->settings = Json::encode($widget);

                    $dbWidget->update();
                }
            }

            $this->addResponse('Dashboard widgets updated.', 0);
        } catch (\Exception $e) {
            $this->addResponse('Error updating dashboard widgets.', 1);
        }
    }

    public function removeWidgetFromDashboard(array $data)
    {
        $this->modelToUse = $this->useModel(BasepackagesDashboardsWidgets::class);

        $widget = $this->getFirst('id', $data['id']);

        if ($widget && $widget->count() > 0) {
            try {
                $widget->delete();

                $this->addResponse('Widget removed from dashboard.', 0);
            } catch (\Exception $e) {
                $this->addResponse('Error removing widget from dashboard.', 1);
            }
        } else {
            $this->addResponse('Error removing widget from dashboard.', 1);
        }
    }
}