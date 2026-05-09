<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Exceptions\IdNotFoundException;
use System\Base\Providers\AccessServiceProvider\Exceptions\PermissionDeniedException;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards\BasepackagesDashboardsWidgets;

class Dashboards extends BasePackage
{
    protected $modelToUse = BasepackagesDashboards::class;

    public $dashboards;

    protected $maxWidgetsPerDashboard = 10;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('dashboards', 'core', $this->domains->getDomain()['name'])) {
                $this->dashboards = $this->opCache->getCache('dashboards', 'core', $this->domains->getDomain()['name']);
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('dashboards', $this->dashboards, 'core', $this->domains->getDomain()['name']);
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function getDashboardById(int $id, $getwidgets = true)
    {
        if (isset($this->dashboards[$id]['widgets'])) {
            return $this->dashboards[$id];
        }

        $this->setFFRelations(true);

        $this->getFirst('id', $id);

        if ($this->config->databasetype === 'db') {
            $dashboard = $this->model->toArray();
            $dashboard['settings'] = $this->helper->decode($dashboard['settings'], true);

            if ($getwidgets) {
                if ($this->model->getwidgets()) {
                    $dashboard['widgets'] = $this->model->getwidgets()->toArray();
                }
            }
        } else {
            if ($this->ffData) {
                $dashboard = $this->ffData;
            }
        }

        if (isset($dashboard)) {
            $this->dashboards[$id] = $dashboard;

            if ($this->opCache) {
                $this->opCache->setCache('dashboards', $this->dashboards, 'core', $this->domains->getDomain()['name']);
            }

            return $dashboard;
        }

        return false;
    }

    public function getDashboardsByAppType($appType)
    {
        $dashboards = [];

        foreach($this->dashboards as $dashboard) {
            if ($dashboard['app_type'] == $appType) {
                array_push($dashboards, $dashboard);
            }
        }

        $this->addResponse('Ok', 0, ['dashboards' => $dashboards]);

        return $dashboards;
    }

    public function addDashboard(array $data)
    {
        if (!isset($data['app_type'])) {
            $data['app_type'] = $this->apps->app['app_type'];
        }

        $data['created_by'] = 0;
        if ($this->access->auth->account()) {
            $data['created_by'] = $this->access->auth->account()['id'];
        }

        $data['settings']['maxWidgetsPerDashboard'] = $this->maxWidgetsPerDashboard;

        if (isset($data['user_default']) && $data['user_default'] == true) {
            if ($this->access->auth->account()) {
                $data['user_default'] = [$this->access->auth->account()['id']];
            }
        } else {
            $data['user_default'] = [];
        }

        $data = $this->getSharedIds($data);

        if ($this->add($data)) {
            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
            }

            $this->addResponse('Dashboard Added');
        } else {
            $this->addResponse('Error Adding Dashboard', 1);
        }
    }

    public function updateDashboard(array $data)
    {
        if ($this->apps->app['settings']['defaultDashboard'] == $data['id']) {
            return true;
        }

        $dashboard = $this->getDashboardById($data['id']);

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

        if ($this->access->auth->account() &&
            $this->access->auth->account()['id'] != $dashboard['created_by']
        ) {
            throw new PermissionDeniedException;
        }

        if (is_string($dashboard['user_default'])) {
            $dashboard['user_default'] = $this->helper->decode($dashboard['user_default'], true);
        }
        if (isset($data['user_default'])) {
            if ($data['user_default'] == true) {
                if ($this->access->auth->account()) {
                    if ($dashboard['user_default'] && !in_array($this->access->auth->account()['id'], $dashboard['user_default'])) {
                        array_push($dashboard['user_default'], $this->access->auth->account()['id']);
                    } else {
                        $dashboard['user_default'] = [$this->access->auth->account()['id']];
                    }
                }
            } else if ($data['user_default'] == false) {
                if ($this->access->auth->account()) {
                    if ($dashboard['user_default'] && in_array($this->access->auth->account()['id'], $dashboard['user_default'])) {
                        $key = array_search($this->access->auth->account()['id'], $dashboard['user_default']);

                        if ($key !== false) {
                            unset($dashboard['user_default'][$key]);
                        }
                    }
                }
            }
        }
        unset($data['user_default']);
        $data = array_merge($dashboard, $data);

        $data = $this->getSharedIds($data);

        if ($this->update($data)) {
            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
            }

            $this->addResponse('Dashboard Updated');
        } else {
            $this->addResponse('Error Updating Dashboard', 1);
        }
    }

    protected function getSharedIds($data)
    {
        if (isset($data['shared']) && is_string($data['shared'])) {
            try {
                $data['shared'] = $this->helper->decode($data['shared'], true);

                if (isset($data['shared']['data'])) {
                    $data['shared'] = $data['shared']['data'];
                }
            } catch (\throwable $e) {
                $data['shared'] = null;
            }
        } else {
            $data['shared'] = null;
        }

        return $data;
    }

    public function removeDashboard(array $data)
    {
        $dashboard = $this->getById($data['id']);

        if ($this->apps->app['settings']['defaultDashboard'] == $data['id']) {
            $this->addResponse('Cannot remove app default dashboard.', 1);

            return false;
        }

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

        if ($this->access->auth->account() &&
            $this->access->auth->account()['id'] != $dashboard['created_by']
        ) {
            throw new PermissionDeniedException;
        }

        if (isset($this->apps->getAppInfo()['settings']['defaultDashboard'])) {
            if ($this->apps->getAppInfo()['settings']['defaultDashboard'] == $dashboard['id']) {
                $this->addResponse('Cannot remove app default dashboard', 1);

                return false;
            }
        }

        //Remove widgets
        $widgets = $this->getDashboardWidgets(['dashboard_id' => $data['id']]);

        if ($widgets && count($widgets) > 0) {
            foreach ($widgets as $widget) {
                $this->removeWidgetFromDashboard(['dashboard_id' => $data['id'], 'id' => $widget['id']]);
            }
        }

        if ($this->remove($data['id'])) {
            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
            }

            $this->addResponse('Dashboard Removed');
        } else {
            $this->addResponse('Error Removing Dashboard', 1);
        }
    }

    public function getDashboardWidgetById(int $id, int $dashboardId)
    {
        $dashboard = $this->getDashboardById($dashboardId);

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

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

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

        if ($this->access->auth->account() &&
            $this->access->auth->account()['id'] != $dashboard['created_by']
        ) {
            throw new PermissionDeniedException;
        }

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

            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
                $this->opCache->removeCache('widgets', 'core', $this->domains->getDomain()['name']);
            }
        } catch (\Exception $e) {
            $this->addResponse('Could not add widget to dashboard.', 1);
        }
    }

    public function updateWidgetToDashboard(array $data)
    {
        $dashboard = $this->getDashboardById($data['dashboard_id']);

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

        if ($this->access->auth->account() &&
            $this->access->auth->account()['id'] != $dashboard['created_by']
        ) {
            throw new PermissionDeniedException;
        }

        $this->modelToUse = $this->useModel(BasepackagesDashboardsWidgets::class);

        $this->setFfStoreToUse();
        $this->ffStore = null;

        try {
            $sequence = 0;

            foreach ($data['widgets'] as $key => $widget) {
                $dbWidget = $this->getFirst('id', $widget['id']);

                if ($dbWidget) {
                    unset($widget['id']);

                    $dbWidgetArr = $dbWidget->toArray();

                    $dbWidgetArr['settings'] = array_merge($dbWidgetArr['settings'], $widget);

                    $dbWidgetArr['sequence'] = $sequence;

                    if ($this->config->databasetype === 'db') {
                        $dbWidget->assign($dbWidgetArr);

                        $dbWidget->update();
                    } else {
                        $this->update($dbWidgetArr);
                    }

                    $sequence++;
                }
            }

            if (!isset($data['dashboard_id']) && count($data['widgets']) === 1) {
                $widget = $this->basepackages->widgets->getWidget($dbWidget['widget_id']);

                $this->addResponse('Widget ' . $widget['name'] . ' updated.', 0);
            } else {
                $this->addResponse('Dashboard widgets updated.', 0);
            }

            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
                $this->opCache->removeCache('widgets', 'core', $this->domains->getDomain()['name']);
            }
        } catch (\Exception $e) {
            $this->addResponse('Error updating dashboard widgets.', 1);
        }
    }

    public function removeWidgetFromDashboard(array $data)
    {
        $dashboard = $this->getDashboardById($data['dashboard_id']);

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

        if ($this->access->auth->account() &&
            $this->access->auth->account()['id'] != $dashboard['created_by']
        ) {
            throw new PermissionDeniedException;
        }

        $this->modelToUse = $this->useModel(BasepackagesDashboardsWidgets::class);

        $this->setFfStoreToUse();
        $this->ffStore = null;

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

            if ($this->opCache) {
                $this->opCache->removeCache('dashboards', 'core', $this->domains->getDomain()['name']);
                $this->opCache->removeCache('widgets', 'core', $this->domains->getDomain()['name']);
            }
        } else {
            $this->addResponse('Error removing widget from dashboard.', 1);
        }
    }

    public function getDashboardWidgets(array $data)
    {
        $dashboard = $this->getDashboardById($data['dashboard_id']);

        if (!$dashboard) {
            throw new IdNotFoundException;
        }

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

                if ($widget) {
                    $widgetsData[$key]['widget'] = $widget;
                } else {
                    unset($dashboard['widgets'][$key]);
                    unset($widgetsData[$key]);
                }
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