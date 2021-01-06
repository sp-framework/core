<?php

namespace Applications\Dash\Components\Users\Accounts;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class AccountsComponent extends BaseComponent
{
    use DynamicTable;
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $account = $this->accounts->generateViewData($this->getData()['id']);
            } else {
                $account = $this->accounts->generateViewData();
            }

            if ($account) {
                $this->view->components = $this->accounts->packagesData->components;

                $this->view->acls = $this->accounts->packagesData->acls;

                $this->view->account = $this->accounts->packagesData->account;

                $this->view->applications = $this->accounts->packagesData->applications;

                $this->view->roles = $this->accounts->packagesData->roles;

                $this->view->canEmail = $this->accounts->packagesData->canEmail;
            }

            $this->view->responseCode = $this->accounts->packagesData->responseCode;

            $this->view->responseMessage = $this->accounts->packagesData->responseMessage;

            $this->view->pick('accounts/view');

            return;
        }

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
                    'edit'      => 'users/accounts',
                    'remove'    => 'users/accounts/remove'
                ]
            ];

        $this->generateDTContent(
            $this->accounts,
            'users/accounts/view',
            null,
            ['email', 'role_id'],
            true,
            ['email', 'role_id'],
            $controlActions,
            ['role_id' => 'role (ID)'],
            $replaceColumns,
            'email'
        );

        $this->view->pick('accounts/list');
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

            $this->accounts->addAccount($this->postData());

            $this->view->responseCode = $this->accounts->packagesData->responseCode;

            $this->view->responseMessage = $this->accounts->packagesData->responseMessage;

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

            $this->accounts->updateAccount($this->postData());

            $this->view->responseCode = $this->accounts->packagesData->responseCode;

            $this->view->responseMessage = $this->accounts->packagesData->responseMessage;

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

            $this->accounts->removeAccount($this->postData());

            $this->view->responseCode = $this->accounts->packagesData->responseCode;

            $this->view->responseMessage = $this->accounts->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}