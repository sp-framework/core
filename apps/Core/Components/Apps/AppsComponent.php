<?php

namespace Apps\Core\Components\Apps;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
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
                $app = $this->apps->getAppById($this->getData()['id']);

                if (!$app) {
                    return $this->throwIdNotFound();
                }

                if (!isset($app['default_component']) ||
                    isset($app['default_component']) && $app['default_component'] == '0'
                ) {
                    if ($app['app_type'] === 'core' || $app['app_type'] === 'dash') {
                        $dashboard = $this->modules->components->getComponentByNameForAppType('dashboards', $app['app_type']);

                        if ($dashboard) {
                            $app['default_component'] = $dashboard['id'];
                        }
                    } else {
                        $home = $this->modules->components->getComponentByNameForAppType('home', $app['app_type']);

                        if ($home) {
                            $app['default_component'] = $home['id'];
                        }
                    }
                }

                if (!isset($app['errors_component']) ||
                    isset($app['errors_component']) && $app['errors_component'] == '0'
                ) {
                    $errors = $this->modules->components->getComponentByNameForAppType('errors', $app['app_type']);

                    if ($errors) {
                        $app['errors_component'] = $errors['id'];
                    } else {
                        $home = $this->modules->components->getComponentByNameForAppType('home', $app['app_type']);

                        if ($home) {
                            $app['errors_component'] = $home['id'];
                        }
                    }
                }

                if (isset($app['can_login_role_ids'])) {
                    if (is_string($app['can_login_role_ids'])) {
                        $app['can_login_role_ids'] = $this->helper->decode($app['can_login_role_ids'], true);
                    }

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
                    if (is_string($app['acceptable_usernames'])) {
                        $app['acceptable_usernames'] = $this->helper->decode($app['acceptable_usernames'], true);
                    }
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
                    if (is_string($app['menu_structure'])) {
                        $app['menu_structure'] = $this->helper->decode($app['menu_structure'], true);
                    }

                    if (count($app['menu_structure']) > 0) {
                        $this->view->menuBaseStructure =
                            $this->seqMenu(array_replace_recursive($this->view->menuBaseStructure, $app['menu_structure']));
                    } else {
                        $app['menu_structure'] = null;
                    }
                }

                if ($app['settings']) {
                    if (is_string($app['settings'])) {
                        $app['settings'] = $this->helper->decode($app['settings'], true);
                    }
                }

                $this->view->modulesMenus = $this->basepackages->menus->getMenusForApp($app['id']);

                //Components
                $componentsArr = $this->modules->components->getComponentsForAppType($app['app_type']);
                foreach ($componentsArr as $key => &$componentValue) {
                    if ($componentValue['apps']) {
                        if (is_string($componentValue['apps'])) {
                            $componentValue['apps'] = $this->helper->decode($componentValue['apps'], true);
                        }

                        if (!isset($componentValue['apps'][$app['id']]['needAuth'])) {
                            $componentValue['apps'][$app['id']]['needAuth'] = false;
                        }
                    }

                    if (isset($dashboard) && $dashboard) {
                        if ($dashboard['id'] == $componentValue['id']) {
                            $componentValue['apps'][$app['id']]['enabled'] = true;
                        }
                    }

                    if ($componentValue['settings']) {
                        if (is_string($componentValue['settings'])) {
                            $componentValue['settings'] = $this->helper->decode($componentValue['settings'], true);
                        }

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

                //Middlewares
                $middlewaresArr = $this->modules->middlewares->getMiddlewaresForAppType($app['app_type'], $app['id']);
                foreach ($middlewaresArr as $key => &$middlewareValue) {
                    if ($middlewareValue['apps']) {
                        if (is_string($middlewareValue['apps'])) {
                            $middlewareValue['apps'] = $this->helper->decode($middlewareValue['apps'], true);
                        }
                    }
                    if ($middlewareValue['settings']) {
                        if (is_string($middlewareValue['settings'])) {
                            $middlewareValue['settings'] = $this->helper->decode($middlewareValue['settings'], true);
                        }

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

                //Views
                $viewsArr = $this->modules->views->getViewsForAppType($app['app_type']);
                if (count($viewsArr) === 1) {
                    array_push($mandatoryViews, $this->helper->first($viewsArr)['name']);

                    $views[$this->helper->first($viewsArr)['id']] = $this->helper->first($viewsArr);

                    if ($views[$this->helper->first($viewsArr)['id']]['apps']) {
                        if (is_string($views[$this->helper->first($viewsArr)['id']]['apps'])) {
                            $views[$this->helper->first($viewsArr)['id']]['apps'] =
                                $this->helper->decode($views[$this->helper->first($viewsArr)['id']]['apps'], true);
                        }
                    }

                    if ($views[$this->helper->first($viewsArr)['id']]['settings']) {
                        if (is_string($views[$this->helper->first($viewsArr)['id']]['settings'])) {
                            $views[$this->helper->first($viewsArr)['id']]['settings'] =
                                $this->helper->decode($views[$this->helper->first($viewsArr)['id']]['settings'], true);
                        }
                    }
                } else {
                    foreach ($viewsArr as $key => &$viewValue) {
                        if ($viewValue['base_view_module_id'] != '0') {
                            continue;
                        }

                        if ($viewValue['apps']) {
                            if (is_string($viewValue['apps'])) {
                                $viewValue['apps'] = $this->helper->decode($viewValue['apps'], true);
                            }
                        }

                        if ($viewValue['settings']) {
                            if (is_string($viewValue['settings'])) {
                                $viewValue['settings'] = $this->helper->decode($viewValue['settings'], true);
                            }

                            if (isset($viewValue['settings']['mandatory']) &&
                                $viewValue['settings']['mandatory'] == true
                            ) {
                                array_push($mandatoryViews, $viewValue['name']);
                            } else if (isset($viewValue['settings']['mandatory'][$app['route']]) &&
                                       $viewValue['settings']['mandatory'][$app['route']] === true
                            ) {
                                array_push($mandatoryViews, $viewValue['name']);
                            }
                        }

                        $views[$key] = $viewValue;
                    }
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
                $this->view->acceptableUsernames = $this->apps->getAcceptableUsernamesForAppId();
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

    public function apiViewAction()
    {
        // var_dump('me');
    }

    /**
     * @acl(name=add)
     * @notification(name=add)
     * @notification_allowed_methods(email)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $viewsArr = $this->modules->views->getViewsForAppType($this->postData()['app_type']);

        if (count($viewsArr) === 0) {
            $this->addResponse('No Views Available for app type ' . $this->postData()['app_type'] . ' cannot proceed!', 1);

            return;
        }

        $this->apps->addApp($this->postData());

        $this->addResponse(
            $this->apps->packagesData->responseMessage,
            $this->apps->packagesData->responseCode,
            $this->apps->packagesData->responseData
        );

        $this->addToNotification('add', 'Added new app ' . $this->postData()['name'], null, $this->apps->packagesData->last);
    }

    /**
     * @acl(name=update)
     * @notification(name=update)
     * @notification_allowed_methods(email)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->apps->updateApp($this->postData());

        $this->addResponse(
            $this->apps->packagesData->responseMessage,
            $this->apps->packagesData->responseCode
        );

        $this->addToNotification('update', 'Updated app ' . $this->postData()['name'], null, $this->apps->packagesData->last);
    }

    /**
     * @acl(name=remove)
     * @notification(name=remove)
     * @notification_allowed_methods(email)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->apps->removeApp($this->postData());

        $this->addResponse(
            $this->apps->packagesData->responseMessage,
            $this->apps->packagesData->responseCode
        );

        $this->addToNotification('remove', 'Removed app with ID ' . $this->postData()['id']);
    }

    public function getFiltersAction()
    {
        $this->requestIsPost();

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
                $user = $this->basepackages->accounts->getAccountById($filter['added_by']);

                if ($user && isset($user['profile']['full_name'])) {
                    $filter['added_by'] = $user['profile']['full_name'];
                } else {
                    $filter['added_by'] = "System";
                }
            }

            $filter['actions'] = '';
        }

        $this->view->data = $filters;
    }

    public function addFilterAction()
    {
        $this->requestIsPost();

        $this->apps->ipFilter->addFilter($this->postData());

        $this->addResponse(
            $this->apps->ipFilter->packagesData->responseMessage,
            $this->apps->ipFilter->packagesData->responseCode
        );
    }

    public function removeFilterAction()
    {
        $this->requestIsPost();

        $this->apps->ipFilter->removeFilter($this->postData());

        $this->addResponse(
            $this->apps->ipFilter->packagesData->responseMessage,
            $this->apps->ipFilter->packagesData->responseCode
        );
    }

    public function allowFilterAction()
    {
        $this->requestIsPost();

        $this->apps->ipFilter->allowFilter($this->postData());

        $this->addResponse(
            $this->apps->ipFilter->packagesData->responseMessage,
            $this->apps->ipFilter->packagesData->responseCode
        );
    }

    public function blockFilterAction()
    {
        $this->requestIsPost();

        $this->apps->ipFilter->blockFilter($this->postData());

        $this->addResponse(
            $this->apps->ipFilter->packagesData->responseMessage,
            $this->apps->ipFilter->packagesData->responseCode
        );
    }

    public function resetAppFiltersAction()
    {
        $this->requestIsPost();

        $this->apps->ipFilter->resetAppFilters($this->postData());

        $this->addResponse(
            $this->apps->ipFilter->packagesData->responseMessage,
            $this->apps->ipFilter->packagesData->responseCode
        );
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
        $this->requestIsPost();

        $viewsArr = $this->modules->views->getViewsForAppType($this->postData()['app_type']);

        if ($viewsArr && count($viewsArr) > 0) {
            $views = [];

            foreach ($viewsArr as $key => &$viewValue) {
                if ($viewValue['base_view_module_id'] != '0') {
                    continue;
                }

                $views[$key] = $viewValue;
            }

            $this->addResponse('Ok', 0, ['views' => $views]);

            return $views;
        } else {
            $this->addResponse('No Views Installed', 1);
        }
    }
}