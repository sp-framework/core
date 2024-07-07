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
            if ($this->getData()['widgets'] == 'info') {
                $this->getNewToken();
                return $this->basepackages->widgets->getWidget($this->getData()['id'], 'info')['info'];
            } else if ($this->getData()['widgets'] == 'content') {
                $dashboardWidget = $this->basepackages->dashboards->getDashboardWidgetById($this->getData()['id'], $this->getData()['did']);
                $dashboardWidget['getWidgetData'] = true;

                $this->getNewToken();
                return $this->basepackages->widgets->getWidget($this->getData()['wid'], 'content', $dashboardWidget)['content'];
            }
        } else {
            if (isset($this->getData()['id'])) {
                $dashboardId = $this->getData()['id'];
            } else {
                $app = $this->apps->getAppInfo();

                if (is_string($app['settings'])) {
                    $app['settings'] = $this->helper->decode($app['settings'], true);
                }

                if (isset($app['settings']['defaultDashboard'])) {
                    $dashboardId = $app['settings']['defaultDashboard'];
                }
            }
        }

        $dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId, true, false);

        $this->view->setViewsDir($this->modules->views->getPhalconViewPath() . $this->getURI());

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

    public function getWidgetContentAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->getWidgetContent($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode,
            $this->basepackages->dashboards->packagesData->responseData
        );
    }
}