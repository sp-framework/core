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
        $this->view->categories = $this->modules->applications->getAppCategories();

        $this->view->subCategories = $this->modules->applications->getAppSubCategories();

        if (isset($this->getData()['id'])) {

            if ($this->getData()['id'] != 0) {
                $application = $this->modules->applications->getById($this->getData()['id']);

                $application['can_login_role_ids'] = Json::decode($application['can_login_role_ids'], true);

                $this->view->application = $application;

                $this->view->components = $this->modules->components->getComponentsForApplication($this->getData()['id']);

                $this->view->middlewares =
                    msort($this->modules->middlewares->getMiddlewaresForApplication($this->getData()['id']), 'sequence');

                $this->view->views = $this->modules->views->getViewsForApplication($this->getData()['id']);
            } else {
                $this->view->middlewares = [];
            }

            $this->view->roles = $this->roles->init()->roles;

            $this->view->emailservices = $this->emailservices->init()->emailservices;

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
                'modules'    => [
                    'title'     => 'modules',
                    'icon'      => 'th',
                    'link'      => 'modules'
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
                        'admin' => 'Admin',
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