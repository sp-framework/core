<?php

namespace Applications\Admin\Components\Roles;

use System\Base\BaseComponent;

class RolesComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $role = $this->roles->generateViewData($this->getData()['id']);
            } else {
                $role = $this->roles->generateViewData();
            }

            if ($role) {
                $this->view->components = $this->roles->packagesData->components;

                $this->view->acls = $this->roles->packagesData->acls;

                $this->view->role = $this->roles->packagesData->role;

                $this->view->applications = $this->roles->packagesData->applications;

                $this->view->roles = $this->roles->packagesData->roles;
            }

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

            $this->view->pick('roles/view');

            return;
        }

        $roles = $this->roles->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'roles',
                    'remove'    => 'roles/remove'
                ]
            ];

        $this->generateDTContent(
            $roles,
            'roles/view',
            null,
            ['name', 'description'],
            true,
            ['name', 'description'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('roles/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->roles->addRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->roles->updateRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->roles->removeRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}