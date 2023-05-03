<?php

namespace Apps\Core\Components\Apps;

use Apps\Core\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class AppsComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $typesArr = $this->apps->types->types;

        $this->view->types = $typesArr;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $app = $this->apps->getIdApp($this->getData()['id']);

                if (!$app) {
                    return $this->throwIdNotFound();
                }

                if (!isset($app['default_component']) ||
                    isset($app['default_component']) && $app['default_component'] == '0'
                ) {
                    if ($app['app_type'] === 'dash') {
                        $dashboard = $this->modules->components->getComponentByName('dashboards');

                        if ($dashboard) {
                            $app['default_component'] = $dashboard['id'];
                        }
                    }
                }

                if (!isset($app['errors_component']) ||
                    isset($app['errors_component']) && $app['errors_component'] == '0'
                ) {
                    $errors = $this->modules->components->getComponentByName('errors');

                    if ($errors) {
                        $app['errors_component'] = $errors['id'];
                    }
                }

                if (isset($app['can_login_role_ids'])) {
                    $app['can_login_role_ids'] = Json::decode($app['can_login_role_ids'], true);

                    if (isset($app['can_login_role_ids']['data'])) {
                        $app['can_login_role_ids'] = $app['can_login_role_ids']['data'];
                    }
                }

                if (!isset($app['guest_role_id']) ||
                    isset($app['guest_role_id']) && $app['guest_role_id'] == '0'
                ) {
                    $app['guest_role_id'] = '3';
                }

                if (isset($app['acceptable_usernames']) && $app['acceptable_usernames'] !== '') {
                    $app['acceptable_usernames'] = Json::decode($app['acceptable_usernames'], true);
                }

                $components = [];
                $middlewares = [];
                $views = [];
                $mandatoryComponents = [];
                $mandatoryMiddlewares = [];
                $mandatoryViews = [];

                $baseMenuStructure = $this->basepackages->menus->getMenusForAppType($app['app_type']);

                $this->view->menuBaseStructure = $baseMenuStructure;

                if ($app['menu_structure']) {
                    $app['menu_structure'] = Json::decode($app['menu_structure'], true);

                    if (count($app['menu_structure']) > 0) {
                        $this->view->menuBaseStructure =
                            $this->seqMenu(array_replace_recursive($this->view->menuBaseStructure, $app['menu_structure']));
                    } else {
                        $app['menu_structure'] = null;
                    }
                }

                if ($app['settings']) {
                    $app['settings'] = Json::decode($app['settings'], true);
                }

                $this->view->modulesMenus = $this->basepackages->menus->getMenusForApp($app['id']);

                $componentsArr = $this->modules->components->getComponentsForAppType($app['app_type']);

                foreach ($componentsArr as $key => &$componentValue) {
                    if ($componentValue['apps']) {
                        $componentValue['apps'] = Json::decode($componentValue['apps'], true);

                        if (!isset($componentValue['apps'][$app['id']]['needAuth'])) {
                            $componentValue['apps'][$app['id']]['needAuth'] = false;
                        }
                    }

                    if (isset($dashboard)) {
                        if ($dashboard['id'] == $componentValue['id']) {
                            $componentValue['apps'][$app['id']]['enabled'] = true;
                        }
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

                        if (isset($componentValue['settings']['needAuth']) &&
                             $componentValue['settings']['needAuth'] === 'disabled'
                        ) {
                            $componentValue['apps'][$app['id']]['needAuth'] = 'disabled';
                        } else if (isset($componentValue['settings']['needAuth']) &&
                             $componentValue['settings']['needAuth'] === 'mandatory'
                        ) {
                            $componentValue['apps'][$app['id']]['needAuth'] = 'mandatory';
                        }
                    }
                    $components[$key] = $componentValue;
                }

                $middlewaresArr =
                        $this->modules->middlewares->getMiddlewaresForAppType(
                            $app['app_type'],
                            $app['id']
                        );

                foreach ($middlewaresArr as $key => &$middlewareValue) {
                    if ($middlewareValue['settings']) {
                        $middlewareValue['settings'] = Json::decode($middlewareValue['settings'], true);

                        if (isset($middlewareValue['settings']['mandatory']) &&
                             $middlewareValue['settings']['mandatory'] === true
                        ) {
                            array_push($mandatoryMiddlewares, $middlewareValue['name']);
                        } else if (isset($middlewareValue['settings']['mandatory'][$app['route']]) &&
                             $middlewareValue['settings']['mandatory'][$app['route']] === true
                        ) {
                            array_push($mandatoryMiddlewares, $middlewareValue['name']);
                        }
                    }
                    $middlewares[$key] = $middlewareValue;
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
                $this->view->middlewares = msort($middlewares, 'sequence');
                $this->view->views = msort($views, 'name');
                $this->view->mandatoryComponents = $mandatoryComponents;
                $this->view->mandatoryMiddlewares = $mandatoryMiddlewares;
                $this->view->mandatoryViews = $mandatoryViews;

                $this->view->app = $app;

                if (isset($this->getData()['modules'])) {
                    $this->view->modules = true;

                    $this->disableViewLevel();

                    $this->view->pick('apps/wizard/modules');

                    return;
                }
                $this->view->acceptableUsernames = $this->apps->getAcceptableUsernames($this->getData()['id']);
            } else {
                $this->view->app = null;
                $domains = $this->domains->domains;

                foreach ($domains as &$domain) {
                    $domain = $this->domains->generateViewData();
                }

                $this->view->domains = $this->domains->domains;

                $this->view->emailservices = $this->basepackages->emailservices->init()->emailServices;

                $storages = $this->basepackages->storages->storages;
                $publicStorages = [];
                $privateStorages = [];

                foreach ($storages as $key => $storage) {
                    if ($storage['permission'] === 'public') {
                        $publicStorages[$key] = $storage;
                    } else if ($storage['permission'] === 'private') {
                        $privateStorages[$key] = $storage;
                    }
                }

                $this->view->publicStorages = $publicStorages;

                $this->view->privateStorages = $privateStorages;
            }

            $this->view->dashboards = $this->basepackages->dashboards->init()->dashboards;
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

            $filters = $this->apps->ipFilter->getFilters($this->postData());

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

            $this->apps->ipFilter->addFilter($this->postData());

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

            $this->apps->ipFilter->removeFilter($this->postData());

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

            $this->apps->ipFilter->blockMonitorFilter($this->postData());

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

            $this->apps->ipFilter->resetAppFilters($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    private function seqMenu($menu)
    {
        $menu = msort($menu, 'seq');

        foreach ($menu as $key => &$value) {
            if (isset($value['childs'])) {
                $value['childs'] = $this->seqMenu($value['childs']);
            }
        }

        return $menu;
    }

    public function getViewsForAppTypeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $views = $this->modules->views->getViewsForAppType($this->postData()['app_type']);

            if ($views) {
                $this->addResponse('Ok', 0, ['views' => $views]);
            } else {
                $this->addResponse(
                    $this->modules->views->packagesData->responseMessage,
                    $this->modules->views->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}