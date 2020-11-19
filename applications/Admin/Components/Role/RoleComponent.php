<?php

namespace Applications\Admin\Components\Role;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

/**
 * @RoleComponent
 */
class RoleComponent extends BaseComponent
{
    /**
     * @acl(name="view")
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
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
        // $this->view->disable();
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

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