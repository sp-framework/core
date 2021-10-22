<?php

namespace Apps\Dash\Components\System\Users\Accounts;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Crms\Customers\Customers;
use Apps\Dash\Packages\Hrms\Employees\Employees;
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

        if ($this->checkPackage('Apps\Dash\Packages\Business\Directory\Contacts\Contacts')) {
            $this->contacts = $this->usePackage(Contacts::class);
        }

        if ($this->checkPackage('Apps\Dash\Packages\Hrms\Employees\Employees')) {
            $this->employees = $this->usePackage(Employees::class);
        }

        if ($this->checkPackage('Apps\Dash\Packages\Crms\Customers\Customers')) {
            $this->customers = $this->usePackage(Customers::class);
        }
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $account = $this->accounts->generateViewData($this->getData()['id']);

                if (!$account) {
                    return $this->throwIdNotFound();
                }
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
            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($dataArr);
                    }

                    return $dataArr;
                };
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
            ['package_row_id', 'status', 'email', 'role_id', 'first_name', 'last_name', 'package_name'],
            true,
            ['status', 'email', 'role_id', 'first_name', 'last_name'],
            $controlActions,
            ['role_id' => 'role (ID)', 'package_name' => 'Used By', 'package_row_id' => 'link'],
            $replaceColumns,
            'email'
        );

        $this->view->pick('accounts/list');
    }

    protected function replaceColumns($dataArr)
    {
        $rolesArr = $this->roles->getAll()->roles;

        foreach ($rolesArr as $key => $role) {
            $roles[$role['id']] = $role;
        }

        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatRoles($data, $roles);

            if ($data['package_name'] === 'contacts' &&
                $this->checkPackage('Apps\Dash\Packages\Business\Directory\Contacts\Contacts')
            ) {
                $data = $this->getContactsData($data, $dataKey);
            } else if ($data['package_name'] === 'employees' &&
                       $this->checkPackage('Apps\Dash\Packages\Hrms\Employees\Employees')
            ) {
                $data = $this->getEmployeesData($data, $dataKey);
            } else if ($data['package_name'] === 'customers' &&
                       $this->checkPackage('Apps\Dash\Packages\Crms\Customers\Customers')
            ) {
                $data = $this->getCustomersData($data, $dataKey);
            } else {
                $data['package_row_id'] = '-';
                $data['package_name'] = '-';
            }

            $data['package_name'] = ucfirst($data['package_name']);

            if ($data['status'] != '1') {
                $data['status'] = '<span class="badge badge-secondary text-uppercase">DISABLED</span>';
            } else {
                $data['status'] = '<span class="badge badge-success text-uppercase">ENABLED</span>';
            }
        }

        return $dataArr;
    }

    protected function formatRoles($data, $roles)
    {
        if ($data['role_id'] == '0') {
            $data['role_id'] = '<span class="badge badge-danger text-uppercase">Not Assigned (0)</span>';
        } else {
            $data['role_id'] = $roles[$data['role_id']]['name'] . ' (' . $data['role_id'] . ')';
        }

        return $data;
    }

    protected function getContactsData($data, $rowId = null)
    {
        $contact = $this->contacts->getById($data['package_row_id']);

        if ($contact) {
            $data['first_name'] = $contact['first_name'];
            $data['last_name'] = $contact['last_name'];

            if (isset($rowId)) {
                $data['package_row_id'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url('business/directory/contacts/q/id/' . $data['package_row_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                        <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                    </a>';
            }
        } else {
            $data['package_row_id'] = '-';
            $data['package_name'] = '-';
        }

        return $data;
    }

    protected function getEmployeesData($data, $rowId = null)
    {
        $employee = $this->employees->getById($data['package_row_id']);

        if ($employee) {
            $data['first_name'] = $employee['first_name'];
            $data['last_name'] = $employee['last_name'];

            if (isset($rowId)) {
                $data['package_row_id'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url('hrms/employees/q/id/' . $data['package_row_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                        <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                    </a>';
            }
        } else {
            $data['package_row_id'] = '-';
            $data['package_name'] = '-';
        }

        return $data;
    }

    protected function getCustomersData($data, $rowId = null)
    {
        $customer = $this->customers->getById($data['package_row_id']);

        if ($customer) {
            $data['first_name'] = $customer['first_name'];
            $data['last_name'] = $customer['last_name'];

            if (isset($rowId)) {
                $data['package_row_id'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url('crms/customers/q/id/' . $data['package_row_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                        <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                    </a>';
            }
        } else {
            $data['package_row_id'] = '-';
            $data['package_name'] = '-';
        }

        return $data;
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