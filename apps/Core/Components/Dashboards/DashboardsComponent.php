<?php

namespace Apps\Core\Components\Dashboards;

use System\Base\BaseComponent;

class DashboardsComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['widgets'])) {
            $this->getNewToken();

            if ($this->getData()['widgets'] == 'info') {
                return $this->basepackages->widgets->getWidget($this->getData()['id'], 'info')['info'];
            } else if ($this->getData()['widgets'] == 'content') {//This is when we add the widget via list of widgets in dashboard.
                $dashboardWidget = $this->basepackages->dashboards->getDashboardWidgetById($this->getData()['id'], $this->getData()['did']);

                $dashboardWidget['getWidgetData'] = true;

                return $this->basepackages->widgets->getWidget($this->getData()['wid'], 'content', $dashboardWidget)['content'];
            }
        } else {
            if (isset($this->getData()['id'])) {
                if ($this->getData()['id'] != 0) {
                    $dashboardId = $this->getData()['id'];
                }
            } else {
                if (is_string($this->app['settings'])) {
                    $this->app['settings'] = $this->helper->decode($this->app['settings'], true);
                }

                if (isset($this->app['settings']['defaultDashboard'])) {
                    $dashboardId = $this->app['settings']['defaultDashboard'];
                }
            }

            if (isset($dashboardId)) {
                $dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId, true, false);

                $this->view->setViewsDir($this->modules->views->getPhalconViewPath() . $this->getURI());

                $this->view->dashboard = $dashboard;

                $this->view->dashboards = $this->basepackages->dashboards->dashboards;

                $this->view->widgetsTree = $this->basepackages->widgets->getWidgetsTree();

                return;
            }
        }

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

    /**
     * @acl(name=add)
     */
    public function addWidgetToDashboardAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->addWidgetToDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode,
            $this->basepackages->dashboards->packagesData->responseData
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateWidgetToDashboardAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->updateWidgetToDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeWidgetFromDashboardAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->removeWidgetFromDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

    public function getDashboardWidgetsAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->getDashboardWidgets($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode,
            $this->basepackages->dashboards->packagesData->responseData
        );
    }
}