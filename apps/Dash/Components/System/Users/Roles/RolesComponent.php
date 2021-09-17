<?php

namespace Apps\Dash\Components\System\Users\Roles;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class RolesComponent extends BaseComponent
{
    use DynamicTable;

    protected $roles;

    public function initialize()
    {
        $this->roles = $this->basepackages->roles;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $role = $this->roles->generateViewData($this->getData()['id']);

                if (!$role) {
                    return $this->throwIdNotFound();
                }
            } else {
                $role = $this->roles->generateViewData();
            }

            if ($role) {
                $this->view->components = $this->roles->packagesData->components;

                $this->view->acls = $this->roles->packagesData->acls;

                $this->view->role = $this->roles->packagesData->role;

                $this->view->apps = $this->roles->packagesData->apps;

                $this->view->roles = $this->roles->packagesData->roles;
            }

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

            $this->view->pick('roles/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/users/roles',
                    'remove'    => 'system/users/roles/remove'
                ]
            ];

        $this->generateDTContent(
            $this->roles,
            'system/users/roles/view',
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