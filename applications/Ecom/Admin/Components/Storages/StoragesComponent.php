<?php

namespace Applications\Ecom\Admin\Components\Storages;

use System\Base\BaseComponent;

class StoragesComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
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

                $this->view->canEmail = $this->users->packagesData->canEmail;
            }

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

            $this->view->pick('users/view');

            return;
        }

        $users = $this->users->init();

        if ($this->request->isPost()) {
            $rolesIdToName = [];
            foreach ($this->roles->getAll()->roles as $roleKey => $roleValue) {
                $rolesIdToName[$roleValue['id']] = $roleValue['name'] . ' (' . $roleValue['id'] . ')';
            }

            $replaceColumns =
                [
                    'role_id' => ['html'  => $rolesIdToName]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'users',
                    'remove'    => 'users/remove'
                ]
            ];

        $this->generateDTContent(
            $users,
            'users/view',
            null,
            ['email', 'role_id'],
            true,
            ['email', 'role_id'],
            $controlActions,
            ['role_id' => 'role (ID)'],
            $replaceColumns,
            'email'
        );

        $this->view->pick('users/list');
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