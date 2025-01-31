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
            if (is_string($this->app['settings'])) {
                $this->app['settings'] = $this->helper->decode($this->app['settings'], true);
            }

            if (isset($this->getData()['id'])) {
                $this->view->sharedAccounts = [];

                if ($this->getData()['id'] != 0) {
                    $dashboardId = $this->getData()['id'];

                    $dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId, true, false);
                    $dashboard['is_app_default'] = false;

                    if (isset($this->app['settings']['defaultDashboard'])) {
                        if ($this->app['settings']['defaultDashboard'] == $dashboard['id']) {
                            $dashboard['is_app_default'] = true;
                        }

                        $this->view->isAppDefault = true;
                    }

                    if (isset($dashboard['shared']) && is_string($dashboard['shared'])) {
                        $dashboard['shared'] = $this->helper->decode($dashboard['shared'], true);
                    }

                    if ($dashboard['shared'] && count($dashboard['shared']) > 0) {
                        $sharedAccounts = [];

                        foreach ($dashboard['shared'] as $accountId) {
                            $account = $this->basepackages->accounts->getAccountById($accountId);

                            if ($account) {
                                $sharedAccounts[] =
                                    ['id' => $account['id'], 'email' => $account['email']];
                            }
                        }

                        $this->view->sharedAccounts = $sharedAccounts;
                    } else {
                        $dashboard['shared'] = [];
                    }

                    $this->view->dashboard = $dashboard;
                }

                $this->view->pick('dashboards/dashboards/dashboard');

                return;
            } else {
                if (isset($this->app['settings']['defaultDashboard'])) {
                    $dashboardId = $this->app['settings']['defaultDashboard'];
                }

                $dashboards = $this->basepackages->dashboards->dashboards;
                $dashboards = msort($dashboards, 'is_default');

                if ($this->access->auth->account()) {
                    foreach ($dashboards as $dashboardKey => &$dashboard) {
                        //Check app default dashboard
                        if ($dashboard['id'] == $dashboardId) {
                            $dashboard['name'] = $dashboard['name'] . ' (App Default)';

                            continue;
                        }

                        //Check shared
                        $isShared = false;
                        if ($dashboard['shared']) {
                            if (is_string($dashboard['shared'])) {
                                $dashboard['shared'] = $this->helper->decode($dashboard['shared'], true);
                            }

                            if (in_array($this->access->auth->account()['id'], $dashboard['shared'])) {
                                $dashboard['name'] = $dashboard['name'] . ' (Shared By ' . $this->basepackages->accounts->getAccountById($dashboard['created_by'])['email'] . ')';
                                $isShared = true;
                            }
                        }

                        //Check Creator
                        if ($dashboard['created_by'] != $this->access->auth->account()['id'] &&
                            !$isShared
                        ) {
                            unset($dashboards[$dashboardKey]);
                        }

                        //Default
                        if ($dashboard['is_default'] && !$isShared) {
                            $dashboardId = $dashboard['id'];

                            $dashboard['name'] = $dashboard['name'] . ' (User Default)';
                        }
                    }
                }

                $this->view->dashboard = $this->basepackages->dashboards->getDashboardById($dashboardId, true, false);

                $this->view->dashboards = $dashboards;

                $this->view->widgetsTree = $this->basepackages->widgets->getWidgetsTree();
            }
        }
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->addDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->updateDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->removeDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

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

    public function updateWidgetToDashboardAction()
    {
        $this->requestIsPost();

        $this->basepackages->dashboards->updateWidgetToDashboard($this->postData());

        $this->addResponse(
            $this->basepackages->dashboards->packagesData->responseMessage,
            $this->basepackages->dashboards->packagesData->responseCode
        );
    }

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

    public function searchAccountAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $searchAccounts = $this->basepackages->accounts->searchAccountInternal($searchQuery);

            if ($searchAccounts) {
                $currentAccount = $this->access->auth->account();

                if ($currentAccount) {
                    foreach ($searchAccounts as $accountKey => &$account) {
                        if ($account['id'] == $currentAccount['id']) {
                            unset($accounts[$accountKey]);
                            continue;
                        }

                        $profile = $this->basepackages->profiles->getProfile($account['id']);

                        $account['name'] = $profile['full_name'];
                    }

                    $this->addResponse(
                        $this->basepackages->accounts->packagesData->responseMessage,
                        $this->basepackages->accounts->packagesData->responseCode,
                        ['accounts' => $searchAccounts]
                    );
                } else {
                    $this->addResponse(
                        $this->basepackages->accounts->packagesData->responseMessage,
                        $this->basepackages->accounts->packagesData->responseCode,
                        ['accounts' => $searchAccounts]
                    );
                }
            } else {
                $this->addResponse(
                    $this->basepackages->accounts->packagesData->responseMessage,
                    $this->basepackages->accounts->packagesData->responseCode,
                    ['accounts' => []]
                );
            }
        } else {
            $this->addResponse('search query missing', 1);
        }
    }
}