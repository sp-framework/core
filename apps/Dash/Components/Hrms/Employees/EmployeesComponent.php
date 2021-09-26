<?php

namespace Apps\Dash\Components\Hrms\Employees;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\Business\Locations\Locations;
use Apps\Dash\Packages\Hrms\Designations\HrmsDesignations;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Apps\Dash\Packages\Hrms\Statuses\HrmsStatuses;
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

        $this->entities = $this->usePackage(Entities::class);

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

        if (isset($this->getData()['id'])) {
            $this->view->contractors = $this->usePackage(Vendors::class)->getAll()->vendors;

            $this->view->entities = $this->entities->getAll()->entities;

            $this->view->portraitLink = '';

            if ($this->getData()['id'] != 0) {
                $employee = $this->employees->getEmployeeById($this->getData()['id']);

                if (!$employee) {
                    return $this->throwIdNotFound();
                }

                if ($employee['portrait'] && $employee['portrait'] !== '') {
                    $this->view->portraitLink = $this->links->url('system/storages/q/uuid/' . $employee['portrait'] . '/w/200');
                }

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
                            if ($attachmentInfo['links']) {
                                $attachmentInfo['links'] = Json::decode($attachmentInfo['links'], true);
                            }
                            $attachments[$key] = $attachmentInfo;
                        }
                    }
                    $employee['employment_attachments'] = $attachments;
                }

                $employee['notes'] = $this->basepackages->notes->getNotes('employees', $this->getData()['id']);

                $employee['contact_phone'] = $this->formatNumbers($employee['contact_phone']);
                $employee['contact_mobile'] = $this->formatNumbers($employee['contact_mobile']);
                $employee['contact_fax'] = $this->formatNumbers($employee['contact_fax']);
            } else {
                $employee = [];
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->currencyEnabled()) {
                $this->view->currency = true;
            } else {
                $this->view->currency = false;
            }

            $this->view->currencies = $this->basepackages->geoCountries->currencyEnabled(true);

            $this->view->employee = $employee;

            $this->view->statuses = $statuses;

            $this->view->designations = $designations;

            $this->view->locations = $this->locations->getAll()->locations;

            $this->useStorage('private');

            $this->view->pick('employees/view');

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
                    'edit'      => 'hrms/employees',
                    'remove'    => 'hrms/employees/remove',
                ]
            ];

        $this->generateDTContent(
            $this->employees,
            'hrms/employees/view',
            null,
            ['account_id', 'ref_id', 'account_email', 'first_name', 'last_name', 'designation', 'status'],
            true,
            ['account_id', 'ref_id', 'account_email', 'first_name', 'last_name', 'designation', 'status'],
            $controlActions,
            ['account_id'=>'account', 'ref_id' => 'Employee Id', 'account_email'=>'email'],
            $replaceColumns,
            'first_name'
        );

        $this->view->pick('employees/list');
    }

    protected function replaceColumns($dataArr)
    {
        $statusesArr = $this->statuses->getAll()->hrmsStatuses;
        $designationsArr = $this->designations->getAll()->hrmsDesignations;

        $statuses = [];
        $designations = [];

        foreach ($statusesArr as $statusesKey => $statusesValue) {
            $statuses[$statusesValue['id']] = $statusesValue['name'];
        }
        foreach ($designationsArr as $designationKey => $designationValue) {
            $designations[$designationValue['id']] = $designationValue['name'];
        }

        foreach ($dataArr as $dataKey => &$data) {
            if ($data['status'] != '0') {
                $data['status'] = $statuses[$data['status']];
            }
            if ($data['designation'] != '0') {
                $data['designation'] = $designations[$data['designation']];
            }
            $data = $this->generateAccountLink($dataKey, $data);
        }

        return $dataArr;
    }

    protected function generateAccountLink($rowId, $data)
    {
        if ($data['account_id'] && $data['account_id'] != '0') {
            $data['account_id'] =
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url('system/users/accounts/q/id/' . $data['account_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                    <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                </a>';
        } else {
            $data['account_id'] = '-';
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

            $this->employees->addEmployee($this->postData());

            $this->addResponse(
                $this->employees->packagesData->responseMessage,
                $this->employees->packagesData->responseCode
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

            $this->employees->updateEmployee($this->postData());

            $this->addResponse(
                $this->employees->packagesData->responseMessage,
                $this->employees->packagesData->responseCode
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

            $this->employees->removeEmployee($this->postData());

            $this->addResponse(
                $this->employees->packagesData->responseMessage,
                $this->employees->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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

                $searchAccounts = $this->basepackages->accounts->searchAccountInternal($searchQuery);

                if ($searchAccounts) {
                    $accounts = $this->basepackages->accounts->packagesData->accounts;

                    if (count($accounts) > 0) {
                        foreach ($accounts as $accountKey => $account) {
                            if ($this->employees->checkAccount($account['email'])) {
                                unset($accounts[$accountKey]);
                            }
                        }
                    }

                    $this->addResponse(
                        $this->basepackages->accounts->packagesData->responseMessage,
                        $this->basepackages->accounts->packagesData->responseCode,
                        ['accounts' => $accounts]
                    );
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
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
                    $this->addResponse(
                        $this->employees->packagesData->responseMessage,
                        $this->employees->packagesData->responseCode,
                        ['employees' => $this->employees->packagesData->employees]
                    );
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function searchEmployeeEmailAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchEmployee = $this->employees->searchByEmail($searchQuery);

                if ($searchEmployee) {
                    $this->view->responseCode = $this->employees->packagesData->responseCode;

                    $this->view->employees = $this->employees->packagesData->employees;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchEmployeeIdAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['id']) {
                $searchEmployees = $this->employees->searchById($this->postData()['id']);

                if ($searchEmployees) {
                    $this->addResponse(
                        $this->employees->packagesData->responseMessage,
                        $this->employees->packagesData->responseCode,
                        ['employee' => $this->employees->packagesData->employee]
                    );
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}