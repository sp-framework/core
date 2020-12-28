<?php

namespace Applications\Ecom\Admin\Components\Apps;

use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
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
        if (isset($this->getData()['modules']) && isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $application = $this->modules->applications->getById($this->getData()['id']);

                $application['can_login_role_ids'] = Json::decode($application['can_login_role_ids'], true);

                $this->view->application = $application;

                $components = [];
                $views = [];
                $mandatoryComponents = [];
                $mandatoryViews = [];

                $componentsArr =
                    $this->modules->components->getComponentsForCategoryAndSubcategory(
                        $application['category'],
                        $application['sub_category']
                    );

                foreach ($componentsArr as $key => &$componentValue) {
                    if ($componentValue['applications']) {
                        $componentValue['applications'] = Json::decode($componentValue['applications'], true);
                    }

                    if ($componentValue['settings']) {
                        $componentValue['settings'] = Json::decode($componentValue['settings'], true);

                        if (isset($componentValue['settings']['mandatory']) && $componentValue['settings']['mandatory'] == true) {
                            array_push($mandatoryComponents, $componentValue['name']);
                        }
                    }

                    $components[$key] = $componentValue;
                }

                $viewsArr =
                    $this->modules->views->getViewsForCategoryAndSubcategory(
                        $application['category'],
                        $application['sub_category']
                    );

                foreach ($viewsArr as $key => &$viewValue) {
                    if ($viewValue['applications']) {
                        $viewValue['applications'] = Json::decode($viewValue['applications'], true);
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
                        $this->modules->middlewares->getMiddlewaresForCategoryAndSubcategory(
                            $application['category'],
                            $application['sub_category'],
                            $application['id']
                        ), 'sequence');
            }

            $this->view->modules = true;

            $this->view->pick('apps/view');

            return;
        }

        if (isset($this->getData()['id'])) {

            $this->view->categories = $this->modules->applications->getAppCategories();

            $this->view->subCategories = $this->modules->applications->getAppSubCategories();

            if ($this->getData()['id'] != 0) {
                $application = $this->modules->applications->getById($this->getData()['id']);

                $application['can_login_role_ids'] = Json::decode($application['can_login_role_ids'], true);

                $this->view->application = $application;
            }

            $this->view->roles = $this->roles->init()->roles;

            $this->view->pick('apps/view');

            return;
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

        $replaceColumns =
            [
                'category'  => ['html' =>
                    [
                        'ecom' => 'E-Commerce Management System',
                        'tms' => 'Transport Management System'
                    ]
                ],
                'sub_category'  => ['html' =>
                    [
                        'admin' => 'Administration',
                        'dashboard' => 'Dashboard',
                        'eshop' => 'EShop',
                        'pos' => 'PoS'
                    ]
                ]
            ];

        $this->generateDTContent(
            $this->modules->applications,
            'apps/view',
            null,
            ['name', 'route', 'category', 'sub_category'],
            true,
            [],
            $controlActions,
            [],
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

            $this->modules->applications->addApplication($this->postData());

            $this->view->responseCode = $this->modules->applications->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->applications->packagesData->responseMessage;

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

            $this->modules->applications->updateApplication($this->postData());

            $this->view->responseCode = $this->modules->applications->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->applications->packagesData->responseMessage;

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

            $this->modules->applications->removeApplication($this->postData());

            $this->view->responseCode = $this->modules->applications->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->applications->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}