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

                if ($app['ip_black_list'] && $app['ip_black_list'] !== '') {
                    $app['ip_black_list'] = Json::decode($app['ip_black_list'], true);
                    $app['ip_black_list'] = implode(',', $app['ip_black_list']);
                }

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

            $this->apps->removeApp($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}