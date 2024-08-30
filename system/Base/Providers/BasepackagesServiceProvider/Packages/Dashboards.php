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

    public function getDashboardById(int $id, $getwidgets = true, $getContent = false)
    {
        $this->setFFRelations(true);

        $this->getFirst('id', $id);

        if ($this->config->databasetype === 'db') {
            $dashboard = $this->model->toArray();
            $dashboard['settings'] = $this->helper->decode($dashboard['settings'], true);

            if ($getwidgets) {
                if ($this->model->getwidgets()) {
                    $dashboard['widgets'] = $this->model->getwidgets()->toArray();
                }
                if ($getContent) {
                    $dashboard['widgets'] = $this->basepackages->widgets->getWidgetsContent($dashboard['widgets']);
                }
            }

            return $dashboard;
        } else {
            if ($this->ffData) {
                if ($getContent) {
                    $this->ffData['widgets'] = $this->basepackages->widgets->getWidgetsContent($this->ffData['widgets']);
                }

                return $this->ffData;
            }
        }

        return false;
    }

    public function getDashboardWidgetById(int $id, int $dashboardId, $getContent = false)
    {
        $dashboard = $this->getDashboardById($dashboardId, true, $getContent);

        if (isset($dashboard['widgets']) && count($dashboard['widgets']) > 0) {
            foreach ($dashboard['widgets'] as $key => $widget) {
                if ($id == $widget['id']) {
                    if ($widget['settings']) {
                        if (is_string($widget['settings'])) {
                            $widget['settings'] = $this->helper->decode($widget['settings'], true);
                        }
                    }

                    return $widget;
                }
            }
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
        $dashboardStore = $this->ff->store($dashboardWidgets->getSource());

        try {
            if ($this->config->databasetype === 'db') {
                $dashboardWidgets->assign($this->jsonData($data));
                $dashboardWidgets->create();

                $newWidget = $dashboardWidgets->toArray();
            } else {
                $newWidget = $dashboardStore->insert($this->jsonData($data));
            }

            $newWidget['widget'] = $this->basepackages->widgets->getWidget($newWidget['widget_id'], 'content', $newWidget);

            $this->addResponse('Widget added to dashboard.', 0, $newWidget);
        } catch (\Exception $e) {
            $this->addResponse('Could not add widget to dashboard.', 1);
        }
    }

    public function updateWidgetToDashboard(array $data)
    {
        $this->modelToUse = $this->useModel(BasepackagesDashboardsWidgets::class);

        $this->setFfStoreToUse();

        try {
            foreach ($data['widgets'] as $key => $widget) {
                $dbWidget = $this->getFirst('id', $widget['id']);

                if ($dbWidget) {
                    unset($widget['id']);

                    $dbWidgetArr = $dbWidget->toArray();

                    $dbWidgetArr['settings'] = array_merge($dbWidgetArr['settings'], $widget);

                    if ($this->config->databasetype === 'db') {
                        $dbWidget->assign($dbWidgetArr);

                        $dbWidget->update();
                    } else {
                        $this->update($dbWidgetArr);
                    }
                }
            }

            if (!isset($data['dashboard_id']) && count($data['widgets']) === 1) {
                $widget = $this->basepackages->widgets->getWidget($dbWidget['widget_id']);

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

        $this->setFfStoreToUse();

        $dbWidget = $this->getFirst('id', $data['id']);

        if ($dbWidget) {
            if ($this->config->databasetype === 'db') {
                try {
                    $dbWidget->delete();

                    $this->addResponse('Widget removed from dashboard.', 0);
                } catch (\Exception $e) {
                    $this->addResponse('Error removing widget from dashboard.', 1);
                }
            } else {
                $dbWidgetArr = $dbWidget->toArray();

                $this->remove($dbWidgetArr['id']);
            }
        } else {
            $this->addResponse('Error removing widget from dashboard.', 1);
        }
    }

    public function getDashboardWidgets(array $data)
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

            $widgetsData = [];

            foreach ($dashboard['widgets'] as $key => $dashboardWidget) {
                if (is_string($dashboardWidget['settings'])) {
                    $dashboardWidget['settings'] = $this->helper->decode($dashboardWidget['settings'], true);
                }

                $widgetsData[$key] = $dashboardWidget;

                $widget = $this->basepackages->widgets->getWidget($dashboardWidget['widget_id'], 'content', $dashboardWidget);

                $widgetsData[$key]['widget'] = $widget;
            }

            $widgetsData = msort($widgetsData, 'sequence');

            $this->addResponse(
                'Dashboard widgets',
                0,
                ['widgetsData' => $widgetsData]
            );
        } else {
            $this->addResponse('No widgets', 2, []);
        }
    }
}