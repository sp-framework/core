<?php

namespace Applications\Dash\Components\Hrms\Employees;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Hrms\Employees\Employees;
use Applications\Dash\Packages\Hrms\Designations\HrmsDesignations;
use Applications\Dash\Packages\Hrms\Statuses\HrmsStatuses;
use Applications\Dash\Packages\Locations\Locations;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class EmployeesComponent extends BaseComponent
{
    use DynamicTable;

    protected $employees;

    protected $statuses;

    protected $designations;

    protected $locations;

    public function initialize()
    {
        $this->employees = $this->usePackage(Employees::class);

        $this->statuses = $this->usePackage(HrmsStatuses::class);

        $this->designations = $this->usePackage(HrmsDesignations::class);

        $this->locations = $this->usePackage(Locations::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $statuses = $this->statuses->getAll()->hrmsStatuses;

        $designations = $this->designations->getAll()->hrmsDesignations;

        $locations = $this->locations->getAll()->locations;

        if (isset($this->getData()['id'])) {
            $this->view->portraitLink = '';
            if ($this->getData()['id'] != 0) {
                $employee = $this->employees->getById($this->getData()['id']);

                if ($employee['portrait'] && $employee['portrait'] !== '') {
                    $this->view->portraitLink = $this->links->url('storages/q/uuid/' . $employee['portrait'] . '/w/200');
                }

                $employee['account_email'] = $this->accounts->getById($employee['account_id'])['email'];

                if ($employee['manager_id'] == 0) {
                    $employee['manager_full_name'] = $employee['full_name'];
                } else {
                    $employee['manager_full_name'] = $this->employees->getById($employee['manager_id'])['full_name'];
                }
                if ($employee['hire_manager_id'] == 0) {
                    $employee['hire_manager_full_name'] = '';
                } else {
                    $employee['hire_manager_full_name'] = $this->employees->getById($employee['hire_manager_id'])['full_name'];
                }
                if ($employee['hire_referrer_id'] == 0) {
                    $employee['hire_referrer_full_name'] = '';
                } else {
                    $employee['hire_referrer_full_name'] = $this->employees->getById($employee['hire_referrer_id'])['full_name'];
                }

                if ($employee['contact_address_id']) {
                    $address = $this->basepackages->addressbook->getById($employee['contact_address_id']);

                    unset($address['id']);

                    $employee = array_merge($employee, $address);
                } else {
                    $employee['street_address'] = '';
                    $employee['street_address_2'] = '';
                    $employee['city_id'] = '';
                    $employee['city_name'] = '';
                    $employee['post_code'] = '';
                    $employee['state_id'] = '';
                    $employee['state_name'] = '';
                    $employee['country_id'] = '';
                    $employee['country_name'] = '';
                }

                if ($employee['employment_attachments']) {
                    $attachments = [];

                    $attachmentsArr = Json::decode($employee['employment_attachments'], true);

                    foreach ($attachmentsArr as $key => $attachment) {
                        $attachmentInfo = $this->basepackages->storages->getFileInfo($attachment);
                        if ($attachmentInfo) {
                            $attachments[$key] = $attachmentInfo;
                        }
                    }

                    $employee['employment_attachments'] = $attachments;
                }

                $this->view->employee = $employee;
            }

            $this->view->statuses = $statuses;

            $this->view->designations = $designations;

            $this->view->locations = $locations;

            $this->view->storage = $this->basepackages->storages->getAppStorages()['private'];

            $this->view->pick('employees/view');

            return;
        }

        if ($this->request->isPost()) {
            $statusesToName = [];
            $designationToName = [];

            foreach ($statuses as $statusesKey => $statusesValue) {
                $statusesToName[$statusesValue['id']] = $statusesValue['name'];
            }
            foreach ($designations as $designationKey => $designationValue) {
                $designationToName[$designationValue['id']] = $designationValue['name'];
            }

            $replaceColumns =
                [
                    'status' => ['html'  => $statusesToName],
                    'designation' => ['html'  => $designationToName],
                    'type_id'   => ['html'  =>
                        [
                            '1' => 'Employee',
                            '2' => 'Contractor'
                        ]
                    ],
                    'work_type_id'   => ['html'  =>
                        [
                            '1' => 'Traditional',
                            '2' => 'Remote'
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
                    'edit'      => 'hrms/employees',
                    'remove'    => 'hrms/employees/remove',
                ]
            ];

        $this->generateDTContent(
            $this->employees,
            'hrms/employees/view',
            null,
            ['first_name', 'last_name', 'designation', 'status', 'type_id', 'work_type_id'],
            true,
            ['first_name', 'last_name', 'designation', 'status', 'type_id', 'work_type_id'],
            $controlActions,
            ['type_id' => 'Type', 'work_type_id' => 'Work Type'],
            $replaceColumns,
            'first_name'
        );

        $this->view->pick('employees/list');
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

            $this->employees->addEmployee($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

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

            $this->employees->updateEmployee($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

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

            $this->employees->removeEmployee($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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
                    $this->view->responseCode = $this->accounts->packagesData->responseCode;

                    $this->view->accounts = $this->accounts->packagesData->accounts;
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

                $searchEmployees = $this->employees->searchByFullName($searchQuery);

                if ($searchEmployees) {
                    $this->view->responseCode = $this->employees->packagesData->responseCode;

                    $this->view->employees = $this->employees->packagesData->employees;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}