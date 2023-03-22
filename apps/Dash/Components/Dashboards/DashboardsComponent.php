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
            $this->getNewToken();

            return $this->basepackages->widgets->getWidget($this->getData()['id'], 'info');
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

        $dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId, false);

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

    public function addWidgetToDashboardAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->basepackages->dashboards->addWidgetToDashboard($this->postData());

            $this->addResponse(
                $this->basepackages->dashboards->packagesData->responseMessage,
                $this->basepackages->dashboards->packagesData->responseCode,
                $this->basepackages->dashboards->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getWidgetContentAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $dashboard = $this->basepackages->dashboards->getDashboardById($this->postData()['dashboard_id'], true);

            if (isset($dashboard['widgets']) && count($dashboard['widgets']) > 0) {
                $this->basepackages->widgets->getWidgetsContent($dashboard['widgets']);

                $this->addResponse(
                    $this->basepackages->widgets->packagesData->responseMessage,
                    $this->basepackages->widgets->packagesData->responseCode,
                    $this->basepackages->widgets->packagesData->responseData
                );
            } else {
                $this->addResponse('No widgets', 2);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}