<?php

namespace Apps\Core\Components\System\Users\Accounts;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Core\Packages\Business\Directory\Contacts\Contacts;
use Apps\Core\Packages\Crms\Customers\Customers;
use Apps\Core\Packages\Hrms\Employees\Employees;
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

                if (!$account) {
                    return $this->throwIdNotFound();
                }
            } else {
                $account = $this->accounts->generateViewData();
            }

            if ($account) {
                $app = $this->apps->getAppInfo();

                $middlewares = $this->modules->middlewares->getMiddlewaresForAppType($app['app_type'],null);

                $middlewareEnabledForApps = [];

                $this->view->aclMiddlewareEnabled = false;

                foreach ($middlewares as $key => &$middleware) {
                    if ($middleware['name'] === 'Acl') {
                        if (isset($middleware['apps']) && is_string($middleware['apps'])) {
                            $middleware['apps'] = $this->helper->decode($middleware['apps'], true);

                            foreach ($middleware['apps'] as $appId => $value) {
                                if (isset($value['enabled']) && $value['enabled'] === true) {
                                    array_push($middlewareEnabledForApps, $appId);
                                }
                            }
                        }
                    }
                }

                $components = $this->accounts->packagesData->components;

                if (count($middlewareEnabledForApps) > 0) {
                    $this->view->aclMiddlewareEnabled = true;
                    foreach ($components as $key => $component) {
                        if (!in_array($component['id'], $middlewareEnabledForApps)) {
                            unset($components[$key]);
                        }
                    }
                }

                $this->view->components = $components;

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
            ['profile_package_row_id', 'status', 'email', 'username', 'role_id', 'first_name', 'last_name', 'profile_package_name'],
            true,
            ['status', 'email', 'username', 'role_id', 'first_name', 'last_name'],
            $controlActions,
            ['role_id' => 'role (ID)', 'profile_package_name' => 'Used By', 'profile_package_row_id' => 'link'],
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
            $data = $this->getProfilesData($data, $dataKey);

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

    protected function getProfilesData($data, $rowId = null)
    {
        $profile = null;
        $componentRoute = null;

        $profilePackage = $this->modules->packages->getPackageByName($data['profile_package_name']);

        if ($data['profile_package_name'] === 'UsersProfiles') {
            $profile = $this->basepackages->profiles->getById($data['profile_package_row_id']);
        } else if ($profilePackage) {
            //Get profile information from packages class.
        }

        if ($profilePackage) {
            if (!is_array($profilePackage['settings'])) {
                $profilePackage['settings'] = $this->helper->decode($profilePackage['settings'], true);
            }
            if (isset($profilePackage['settings']['componentRoute'])) {
                $componentRoute = $profilePackage['settings']['componentRoute'];
            }
        }

        if ($profile && $componentRoute) {
            if (isset($rowId)) {
                $data['profile_package_row_id'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url($componentRoute . '/q/aid/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                        <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                    </a>';
            }
        } else {
            $data['profile_package_row_id'] = '-';
            $data['profile_package_name'] = '-';
        }

        return $data;
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->accounts->addAccount($this->postData());

        $this->addResponse(
            $this->accounts->packagesData->responseMessage,
            $this->accounts->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->accounts->updateAccount($this->postData());

        $this->addResponse(
            $this->accounts->packagesData->responseMessage,
            $this->accounts->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->accounts->removeAccount($this->postData());

        $this->addResponse(
            $this->accounts->packagesData->responseMessage,
            $this->accounts->packagesData->responseCode
        );
    }
}