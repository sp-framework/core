<?php

namespace Applications\Dash\Components\Filters;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Hrms\Employees\Employees;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    use DynamicTable;

    protected $filters;

    public function initialize()
    {
        $this->filters = $this->basepackages->filters;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if ($this->application['id'] == 1) {
            $components = $this->modules->components->components;

            foreach ($components as $key => $component) {
                $components[$key]['name'] = $component['name'] . ' (' . $component['category'] . '/' . $component['sub_category'] . ')';
            }

            $this->view->components = $components;

            if (isset($this->getData()['id'])) {
                if ($this->getData()['id'] != 0) {
                    $filter = $this->filters->getById($this->getData()['id']);

                    $this->view->filter = $filter;
                }

                $this->view->pick('filters/view');

                return;
            }

            if ($this->request->isPost()) {
                $replaceColumns =
                    [
                        'filter_type' => ['html'  =>
                            [
                                '0' =>  'System',
                                '1' =>  'User',
                                '2' =>  'User',
                            ]
                        ],
                        'is_default' => ['html'  =>
                            [
                                '0' =>  'No',
                                '1' =>  'Yes'
                            ]
                        ],
                        'auto_generated' => ['html'  =>
                            [
                                '0' =>  'No',
                                '1' =>  'Yes'
                            ]
                        ]
                    ];
            } else {
                $replaceColumns = null;
            }

            $controlActions =
                [
                    'actionsToEnable'       =>
                    [
                        'edit'      => 'filters',
                        'remove'    => 'filters/remove'
                    ]
                ];

            $this->generateDTContent(
                $this->basepackages->filters,
                'filters/view',
                null,
                ['name', 'filter_type', 'auto_generated', 'is_default'],
                true,
                ['name', 'filter_type', 'auto_generated', 'is_default'],
                $controlActions,
                null,
                $replaceColumns,
                'name'
            );

            $this->view->pick('filters/list');
        }
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

            if ($this->application['id'] === 1) {
                $this->filters->addFilter($this->postData());

                $this->view->filters = $this->filters->packagesData->filters;
            } else {
                //Adding close in add as cloning requires add permission so both add and clone can be performed in same action.
                if (isset($this->postData()['clone']) && $this->postData()['clone']) {
                    $this->filters->cloneFilter($this->postData());
                } else {
                    $this->filters->addFilter($this->postData());
                }

                if (isset($this->postData()['component_id'])) {
                    $this->view->filters = $this->filters->packagesData->filters;
                }
            }
            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

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

            $this->filters->updateFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            if ($this->application['id'] == 1) {
                $this->view->filters = $this->filters->packagesData->filters;
            } else {
                if (isset($this->postData()['component_id'])) {
                    $this->view->filters = $this->filters->packagesData->filters;
                }
            }

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    protected function cloneAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->cloneFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            $this->view->filters = $this->filters->packagesData->filters;

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

            if (!$this->checkCSRF()) {
                return;
            }

            $removeFilter = $this->filters->removeFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            if ($removeFilter) {
                if ($this->application['id'] === 1) {
                    $this->view->filters = $this->filters->packagesData->filters;
                } else {
                    if (isset($this->postData()['component_id'])) {
                        $this->view->filters = $this->filters->packagesData->filters;
                    }
                }
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getdefaultfilterAction()
    {
        if ($this->request->isPost()) {

            if ($this->filters->getDefaultFilter($this->postData()['component_id'])) {
                $this->view->defaultFilter = $this->filters->packagesData->defaultFilter;
            }

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchRoleAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchRoles = $this->roles->searchRole($searchQuery);

                if ($searchRoles) {
                    $this->view->responseCode = $this->roles->packagesData->responseCode;

                    $this->view->roles = $this->roles->packagesData->roles;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchAccountAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchAccounts = $this->accounts->searchAccountInternal($searchQuery);

                if ($searchAccounts) {
                    $currentAccount = $this->auth->account();

                    if ($currentAccount) {
                        $accounts = $this->accounts->packagesData->accounts;

                        foreach ($accounts as $accountKey => $account) {
                            if ($account['id'] == $currentAccount['id']) {
                                unset($accounts[$accountKey]);
                            }
                        }

                        $accounts = array_values($accounts);

                        $this->view->responseCode = $this->accounts->packagesData->responseCode;

                        $this->view->accounts = $accounts;
                    } else {
                        $this->view->responseCode = $this->accounts->packagesData->responseCode;

                        $this->view->accounts = $this->accounts->packagesData->accounts;
                    }
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchEmployeeAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $employees = $this->usePackage(Employees::class);

                $searchEmployees = $employees->searchByFullName($searchQuery);

                if ($searchEmployees) {
                    $currentAccount = $this->auth->account();

                    if ($currentAccount) {
                        $employeesArr = $employees->packagesData->employees;

                        foreach ($employeesArr as $employeeKey => $employee) {
                            if ($employee['id'] == $currentAccount['id']) {
                                unset($employeesArr[$employeeKey]);
                            }
                        }

                        $employeesArr = array_values($employeesArr);

                        $this->view->responseCode = $employees->packagesData->responseCode;

                        $this->view->employees = $employeesArr;
                    } else {
                        $this->view->responseCode = $employees->packagesData->responseCode;

                        $this->view->employees = $employees->packagesData->employees;
                    }
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}
