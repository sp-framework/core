<?php

namespace Apps\Dash\Components\Apps;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Installer\Packages\Setup\Schema\Apps\IpBlackList;

class AppsComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $typesArr = $this->apps->types;

        $this->view->types = $typesArr;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $app = $this->apps->getIdApp($this->getData()['id']);

                if (!$app) {
                    return $this->throwIdNotFound();
                }

                if (isset($app['can_login_role_ids'])) {
                    $app['can_login_role_ids'] = Json::decode($app['can_login_role_ids'], true);

                    if (isset($app['can_login_role_ids']['data'])) {
                        $app['can_login_role_ids'] = Json::encode($app['can_login_role_ids']['data']);
                    } else {
                        $app['can_login_role_ids'] = Json::encode($app['can_login_role_ids']);
                    }
                }

                $components = [];
                $views = [];
                $mandatoryComponents = [];
                $mandatoryViews = [];

                $this->view->modulesMenus = $this->basepackages->menus->getMenusForApp($app['id']);

                $componentsArr = $this->modules->components->getComponentsForAppType($app['app_type']);

                foreach ($componentsArr as $key => &$componentValue) {
                    if ($componentValue['apps']) {
                        $componentValue['apps'] = Json::decode($componentValue['apps'], true);
                    }

                    if ($componentValue['settings']) {
                        $componentValue['settings'] = Json::decode($componentValue['settings'], true);

                        if (isset($componentValue['settings']['mandatory']) &&
                             $componentValue['settings']['mandatory'] === true
                        ) {
                            array_push($mandatoryComponents, $componentValue['name']);
                        } else if (isset($componentValue['settings']['mandatory'][$app['route']]) &&
                             $componentValue['settings']['mandatory'][$app['route']] === true
                        ) {
                            array_push($mandatoryComponents, $componentValue['name']);
                        }
                    }
                    $components[$key] = $componentValue;
                }

                $viewsArr = $this->modules->views->getViewsForAppType($app['app_type']);

                foreach ($viewsArr as $key => &$viewValue) {
                    if ($viewValue['apps']) {
                        $viewValue['apps'] = Json::decode($viewValue['apps'], true);
                    }

                    if ($viewValue['settings']) {
                        $viewValue['settings'] = Json::decode($viewValue['settings'], true);

                        if (isset($viewValue['settings']['mandatory']) && $viewValue['settings']['mandatory'] == true) {
                            array_push($mandatoryViews, $viewValue['name']);
                        }
                    }

                    $views[$key] = $viewValue;
                }

                $this->view->components = msort($components, 'name');
                $this->view->views = msort($views, 'name');
                $this->view->mandatoryComponents = $mandatoryComponents;
                $this->view->mandatoryViews = $mandatoryViews;

                $this->view->middlewares =
                    msort(
                        $this->modules->middlewares->getMiddlewaresForAppType(
                            $app['app_type'],
                            $app['id']
                        ), 'sequence');

                $this->view->app = $app;

                if (isset($this->getData()['modules'])) {

                    $this->view->modules = true;

                    $this->disableViewLevel();

                    $this->view->pick('apps/wizard/modules');

                    return;
                }
            }

            $this->view->roles = $this->basepackages->roles->init()->roles;

            $this->view->pick('apps/view');

            return;
        }

        $types = [];

        foreach ($typesArr as $typeKey => $type) {
            $types[$type['app_type']] = $type['name'];
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'apps',
                    'remove'    => 'apps/remove',
                ]
            ];

        $replaceColumns = ['app_type'  => ['html' => $types]];

        $this->generateDTContent(
            $this->apps,
            'apps/view',
            null,
            ['name', 'route', 'app_type'],
            true,
            [],
            $controlActions,
            null,
            $replaceColumns,
            'name',
            null
        );

        $this->view->pick('apps/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->addApp($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode,
                $this->apps->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->updateApp($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->removeApp($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getFiltersAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $filters = $this->apps->getFilters($this->postData());

            foreach ($filters as $key => &$filter) {
                unset ($filter['app_id']);
                if ($filter['address_type'] == '1') {
                    $filter['address_type'] = 'host';
                } else if ($filter['address_type'] == '2') {
                    $filter['address_type'] = 'network';
                }

                if ($filter['filter_type'] == '1') {
                    $filter['filter_type'] = "allow";
                } else if ($filter['filter_type'] == '2') {
                    $filter['filter_type'] = "block";
                } else if ($filter['filter_type'] == '3') {
                    $filter['filter_type'] = "monitor";
                }

                if ($filter['added_by'] == '0') {
                    $filter['added_by'] = "System";
                } else {
                    $user = $this->basepackages->accounts->getAccountById($filter['added_by'],false,false,false,false,false,false,true);

                    if ($user && isset($user['full_name'])) {
                        $filter['added_by'] = $user['full_name'];
                    } else {
                        $filter['added_by'] = "System";
                    }
                }

                $filter['actions'] = '';
            }

            $this->view->data = $filters;
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function addFilterAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->addFilter($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function removeFilterAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->removeFilter($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function blockMonitorFilterAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->blockMonitorFilter($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function resetAppFiltersAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->apps->resetAppFilters($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}