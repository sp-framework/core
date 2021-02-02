<?php

namespace Apps\Dash\Components\Apps;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
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
        $typesArr = $this->apps->types;

        $this->view->types = $typesArr;

        if (isset($this->getData()['modules']) && isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $app = $this->apps->getById($this->getData()['id']);

                $app['can_login_role_ids'] = Json::decode($app['can_login_role_ids'], true);

                $this->view->app = $app;

                $components = [];
                $views = [];
                $mandatoryComponents = [];
                $mandatoryViews = [];

                $this->view->modulesMenus = $this->basepackages->menus->getMenusForApp($app['id']);

                $componentsArr =
                    $this->modules->components->getComponentsForAppType($app['app_type']);

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

                $viewsArr =
                    $this->modules->views->getViewsForAppType($app['app_type']);

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
                // $this->view->components = $components;
                // $this->view->views = $views;
                $this->view->mandatoryComponents = $mandatoryComponents;
                $this->view->mandatoryViews = $mandatoryViews;

                $this->view->middlewares =
                    msort(
                        $this->modules->middlewares->getMiddlewaresForAppType(
                            $app['app_type'],
                            $app['id']
                        ), 'sequence');
            }

            $this->view->modules = true;

            $this->view->pick('apps/view');

            return;
        }

        if (isset($this->getData()['id'])) {

            if ($this->getData()['id'] != 0) {
                $app = $this->apps->getById($this->getData()['id']);

                $app['can_login_role_ids'] = Json::decode($app['can_login_role_ids'], true);

                $this->view->app = $app;
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

        $dtAdditionControlButtons =
            [
                'includeId'  => true,
                // 'includeQ'   => true, //Only true when not adding /q/ in link below.
                'buttons'    => [
                    'modules'    => [
                        'title'     => 'modules',
                        'icon'      => 'th',
                        'link'      => 'apps/q/modules/true'
                    ]
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
            $dtAdditionControlButtons
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

            $this->view->responseCode = $this->apps->packagesData->responseCode;

            $this->view->responseMessage = $this->apps->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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

            $this->view->responseCode = $this->apps->packagesData->responseCode;

            $this->view->responseMessage = $this->apps->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->apps->removeApp($this->postData());

            $this->view->responseCode = $this->apps->packagesData->responseCode;

            $this->view->responseMessage = $this->apps->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}