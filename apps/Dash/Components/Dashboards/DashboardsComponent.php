<?php

namespace Apps\Dash\Components\Dashboards;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class DashboardsComponent extends BaseComponent
{
    public function viewAction()
    {
        if (isset($this->getData()['widgets']) &&
            $this->getData()['widgets'] == true &&
            isset($this->getData()['id'])
        ) {
            return $this->basepackages->widgets->getWidgetInfo($this->getData()['id']);
        } else {
            if (isset($this->getData()['id'])) {
                $dashboardId = $this->getData()['id'];
            } else {
                $app = $this->apps->getAppInfo();

                $app['settings'] = Json::decode($app['settings'], true);

                if (isset($app['settings']['defaultDashboard'])) {
                    $dashboardId = $app['settings']['defaultDashboard'];
                }
            }
        }

        $dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId);

        $dashboard['settings'] = Json::decode($dashboard['settings']);

        $this->view->dashboard = $dashboard;

        $this->view->dashboards = $this->basepackages->dashboards->dashboards;

        $this->view->widgetsTree = $this->basepackages->widgets->getWidgetsTree();
    }

    public function addAction()
    {
        return;
    }

    public function updateAction()
    {
        return;
    }

    public function removeAction()
    {
        return;
    }
}