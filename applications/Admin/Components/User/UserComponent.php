<?php

namespace Applications\Admin\Components\User;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class UserComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $user = $this->users->generateViewData($this->getData()['id']);
        } else {
            $user = $this->users->generateViewData();
        }

        if ($user) {
            $this->view->components = $this->users->packagesData->components;

            $this->view->acls = $this->users->packagesData->acls;

            $this->view->user = $this->users->packagesData->user;

            $this->view->applications = $this->users->packagesData->applications;

            $this->view->roles = $this->users->packagesData->roles;
        }

        $this->view->responseCode = $this->users->packagesData->responseCode;

        $this->view->responseMessage = $this->users->packagesData->responseMessage;
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

            $this->users->addUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

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

            $this->users->updateUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

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

            $this->users->removeUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}