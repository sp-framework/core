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
            $dashboard['settings'] = Json::decode($dashboard['settings'], true);

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
        $dashboard = $this->getDashboardById($data['dashboard_id']);

        $maxWidgetsPerDashboard = 10;

        if (isset($dashboard['settings']['maxWidgetsPerDashboard'])) {
            $maxWidgetsPerDashboard = $dashboard['settings']['maxWidgetsPerDashboard'];
        }

        if (isset($dashboard['widgets']) && count($dashboard['widgets']) >= $maxWidgetsPerDashboard) {
            $this->addResponse('Error: Max widgets for this dashboard reached.', 1);

            return false;
        }

        $widget = $this->basepackages->widgets->getWidget($data['widget_id']);

        if (isset($widget['max_multiple']) && $widget['max_multiple'] != 0) {
            if (isset($dashboard['widgets']) && count($dashboard['widgets']) >= 0) {
                $widgetCounter = 0;
                foreach ($dashboard['widgets'] as $key => $dashboardWidget) {
                    if ($dashboardWidget['widget_id'] == $widget['id']) {
                        $widgetCounter++;
                    }
                }

                if ($widgetCounter != 0 && $widgetCounter >= $widget['max_multiple']) {
                    $this->addResponse('Error: Max instances for this widget per dashboard has reached.', 1);

                    return false;
                }
            }
        }

        $dashboardWidgets = $this->useModel(BasepackagesDashboardsWidgets::class);

        $dashboardWidgets->assign($this->jsonData($data));

        try {
            if ($dashboardWidgets->create()) {
                $newWidget = $dashboardWidgets->toArray();

                $newWidget['widget'] = $this->basepackages->widgets->getWidget($newWidget['widget_id'], 'content', $newWidget);

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

                    $dbWidget->settings = Json::decode($dbWidget->settings, true);
                    $dbWidget->settings = array_merge($dbWidget->settings, $widget);
                    $dbWidget->settings = Json::encode($dbWidget->settings);

                    $dbWidget->update();
                }
            }

            if (!isset($data['dashboard_id'])) {
                $widget = $this->basepackages->widgets->getWidget($dbWidget->widget_id);

                $this->addResponse('Widget ' . $widget['name'] . ' updated.', 0);
            } else {
                $this->addResponse('Dashboard widgets updated.', 0);
            }

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

    public function getWidgetContent(array $data)
    {
        $dashboard = $this->getDashboardById($data['dashboard_id'], true);

        if (isset($dashboard['widgets']) && count($dashboard['widgets']) > 0) {
            if (isset($data['widget_id'])) {
                foreach ($dashboard['widgets'] as $key => $widget) {
                    if ($data['widget_id'] != $widget['id']) {
                        unset($dashboard['widgets'][$key]);
                    }
                }
            }

            $this->basepackages->widgets->getWidgetsContent($dashboard['widgets']);

            $this->addResponse(
                $this->basepackages->widgets->packagesData->responseMessage,
                $this->basepackages->widgets->packagesData->responseCode,
                $this->basepackages->widgets->packagesData->responseData
            );
        } else {
            $this->addResponse('No widgets', 2, []);
        }
    }
}