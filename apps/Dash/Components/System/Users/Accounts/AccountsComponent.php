<?php

namespace Apps\Dash\Components\System\Users\Accounts;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class AccountsComponent extends BaseComponent
{
    use DynamicTable;

    protected $accounts;

    protected $roles;

    public function initialize()
    {
        $this->accounts = $this->basepackages->accounts;

        $this->roles = $this->basepackages->roles;
    }

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

                $this->view->apps = $this->accounts->packagesData->apps;

                $this->view->roles = $this->accounts->packagesData->roles;
            }

            $this->addResponse(
                $this->accounts->packagesData->responseMessage,
                $this->accounts->packagesData->responseCode
            );

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
                    'edit'      => 'system/users/accounts',
                    'remove'    => 'system/users/accounts/remove'
                ]
            ];

        $this->generateDTContent(
            $this->accounts,
            'system/users/accounts/view',
            null,
            ['email', 'role_id', 'first_name', 'last_name'],
            true,
            ['email', 'role_id', 'first_name', 'last_name'],
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

            $this->addResponse(
                $this->accounts->packagesData->responseMessage,
                $this->accounts->packagesData->responseCode
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

            $this->accounts->updateAccount($this->postData());

            $this->addResponse(
                $this->accounts->packagesData->responseMessage,
                $this->accounts->packagesData->responseCode
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

            $this->accounts->removeAccount($this->postData());

            $this->addResponse(
                $this->accounts->packagesData->responseMessage,
                $this->accounts->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}