<?php

namespace Apps\Dash\Packages\Hrms\Employees;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesContact;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesEmployment;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesFinance;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Employees extends BasePackage
{
    protected $modelToUse = HrmsEmployees::class;

    protected $packageName = 'employees';

    public $employees;

    public function getEmployeeById(int $id)
    {
        $employeeModel = new $this->modelToUse;

        $employeeObj = $employeeModel::findFirstById($id);

        $employee = $employeeObj->toArray();

        $financeObj = $employeeObj->getFinance();
        $finance = $financeObj->toArray();
        unset($finance['id']);
        $employee = array_merge($employee, $finance);

        $contactObj = $employeeObj->getContact();
        $contact = $contactObj->toArray();
        unset($contact['id']);
        $employee = array_merge($employee, $contact);

        $employmentObj = $employeeObj->getEmployment();
        $employment = $employmentObj->toArray();
        unset($employment['id']);
        $employee = array_merge($employee, $employment);

        return $employee;
    }

    public function addEmployee(array $data)
    {
        if ($this->checkAccount($data['account_id'])) {
            $this->addResponse('Account ID (email) already in use.', 1);

            return;
        }

        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        if ($data['work_type_id'] == 2) {
            $address = $data;

            $address['name'] = $data['full_name'];

            $address['package_name'] = $this->packageName;

            $address['id'] = '';

            $this->basepackages->addressbook->addAddress($address);

            $data['contact_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
        }
        if ($this->add($data)) {
            $data['id'] = $this->packagesData->last['id'];

            $data = $this->addRefId($data);

            $this->basepackages->storages->changeOrphanStatus($data['portrait']);

            if ($data['employment_attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['employment_attachments'], null, true);
            }

            $data['employee_id'] = $this->packagesData->last['id'];

            $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);

            $data['id'] = $this->packagesData->last['id'];

            $this->update($data);

            $this->addEmployeeFinance($data);
            $this->addEmployeeContact($data);
            $this->addEmployeeEmployment($data);


            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Added ' . $data['full_name'] . ' employee');
        } else {
            $this->addResponse('Error adding new employee.', 1);
        }
    }

    public function updateEmployee(array $data)
    {
        $employee = $this->getEmployeeById($data['id']);

        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        if (isset($data['work_type_id']) && isset($data['contact_address_id'])) {
            if ($data['work_type_id'] == 2 &&
                ($data['contact_address_id'] &&
                 $data['contact_address_id'] != 0 &&
                 $data['contact_address_id'] !== '')
            ) {
                $address = $data;

                $address['package_name'] = $this->packageName;

                $address['name'] = $data['full_name'];

                $address['address_id'] = $data['contact_address_id'];

                $this->basepackages->addressbook->mergeAndUpdate($address);

            } else if ($data['work_type_id'] == 2 &&
                       (!$data['contact_address_id'] ||
                        $data['contact_address_id'] == 0 ||
                        $data['contact_address_id'] === '')
            ) {
                $address = $data;

                $address['package_name'] = $this->packageName;

                $address['name'] = $data['full_name'];

                $address['id'] = '';

                $this->basepackages->addressbook->addAddress($address);

                $data['contact_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
            }
        }

        if ($this->update($data)) {
            $data = $this->addRefId($data);

            $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);

            $data['id'] = $this->packagesData->last['id'];

            $this->update($data);

            $this->updateEmployeeFinance($data);
            $this->updateEmployeeContact($data);
            $this->updateEmployeeEmployment($data);


            $this->basepackages->storages->changeOrphanStatus($data['portrait'], $employee['portrait']);

            if (isset($data['employment_attachments']) && $data['employment_attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['employment_attachments'], $employee['employment_attachments'], true);
            }

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Updated ' . $data['full_name'] . ' employee');
        } else {
            $this->addResponse('Error updating employee.', 1);
        }
    }

    protected function addEmployeeFinance(array $data)
    {
        $this->modelToUse = HrmsEmployeesFinance::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function updateEmployeeFinance(array $data)
    {
        $this->modelToUse = HrmsEmployeesFinance::class;

        $financeModel = $this->modelToUse::findFirst(
            [
                'conditions'    => 'employee_id = :eid:',
                'bind'          => [
                    'eid'       => $data['id']
                ]
            ]
        );

        if ($financeModel) {
            unset($data['id']);

            $finance = $financeModel->toArray();

            $finance = array_merge($finance, $data);

            $this->update($finance);
        }

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function addEmployeeContact(array $data)
    {
        $this->modelToUse = HrmsEmployeesContact::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function updateEmployeeContact(array $data)
    {
        $this->modelToUse = HrmsEmployeesContact::class;

        $contactModel = $this->modelToUse::findFirst(
            [
                'conditions'    => 'employee_id = :eid:',
                'bind'          => [
                    'eid'       => $data['id']
                ]
            ]
        );

        if ($contactModel) {
            unset($data['id']);

            $contact = $contactModel->toArray();

            $contact = array_merge($contact, $data);

            $this->update($contact);
        }

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function addEmployeeEmployment(array $data)
    {
        $this->modelToUse = HrmsEmployeesEmployment::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function updateEmployeeEmployment(array $data)
    {
        $this->modelToUse = HrmsEmployeesEmployment::class;

        $employmentModel = $this->modelToUse::findFirst(
            [
                'conditions'    => 'employee_id = :eid:',
                'bind'          => [
                    'eid'       => $data['id']
                ]
            ]
        );

        if ($employmentModel) {
            unset($data['id']);

            $employment = $employmentModel->toArray();

            $employment = array_merge($employment, $data);

            $this->update($employment);
        }

        $this->modelToUse = HrmsEmployees::class;
    }

    public function checkAccount($id)
    {
        $searchEmployees =
            $this->getByParams(
                [
                    'conditions'    => 'account_id = :aid:',
                    'bind'          => [
                        'aid'       => $id
                    ]
                ]
            );

        if ($searchEmployees && count($searchEmployees) > 0) {
            return true;
        }

        return false;
    }

    public function removeEmployee(array $data)
    {
        $employee = $this->getById($data['id']);

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $employee['portrait']);

            $this->addResponse('Removed employee');
        } else {
            $this->addResponse('Error removing employee.', 1);
        }
    }

    protected function addUpdateAccount($data)
    {
        $data['package_name'] = 'employees';
        $data['package_row_id'] = $data['id'];

        unset($data['id']);

        $data['email'] = $data['account_email'];

        if (isset($data['account_id']) &&
            $data['account_id'] != '' &&
            $data['account_id'] != '0'
        ) {
            $data['id'] = $data['account_id'];

            try {
                $this->basepackages->accounts->updateAccount($data);

                return $this->basepackages->accounts->packagesData->packagesData['last']['id'];
            } catch (\Exception $e) {
                $this->addResponse('Error adding/updating employee account. Please contact administrator', 1);
            }
        } else {
            $data['role_id'] = '0';
            $data['override_role'] = '0';
            $data['permissions'] = '[]';
            $data['can_login'] = '';

            try {
                $this->basepackages->accounts->addAccount($data);

                return $this->basepackages->accounts->packagesData->packagesData['last']['id'];
            } catch (\Exception $e) {
                $this->addResponse('Error adding/updating employee account. Please contact administrator', 1);
            }
        }
    }

    public function searchByFullName(string $nameQueryString)
    {
        $searchEmployees =
            $this->getByParams(
                [
                    'conditions'    => 'full_name LIKE :fullName:',
                    'bind'          => [
                        'fullName'     => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchEmployees) > 0) {
            $employees = [];

            foreach ($searchEmployees as $employeeKey => $employeeValue) {
                $employees[$employeeKey]['id'] = $employeeValue['id'];
                $employees[$employeeKey]['full_name'] = $employeeValue['full_name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->employees = $employees;

            return true;
        }
    }

    public function searchByEmail(string $nameQueryString)
    {
        $searchEmployees =
            $this->getByParams(
                [
                    'conditions'            => 'account_email LIKE :accountEmail:',
                    'bind'                  => [
                        'accountEmail'      => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchEmployees) > 0) {
            $employees = [];

            foreach ($searchEmployees as $employeeKey => $employeeValue) {
                $employees[$employeeKey]['id'] = $employeeValue['id'];
                $employees[$employeeKey]['account_email'] = $employeeValue['account_email'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->employees = $employees;

            return true;
        }
    }

    public function searchByAccountId($accountId)
    {
        $searchEmployee =
            $this->getByParams(
                [
                    'conditions'    => 'account_id = :accountId:',
                    'bind'          => [
                        'accountId' => $accountId
                    ]
                ]
            );

        if (count($searchEmployee) === 1) {
            return $searchEmployee[0];
        }
    }

    public function searchById($id)
    {
        $employee = $this->getById($id);

        if ($employee) {
            $modelToUse = HrmsEmployeesContact::class;

            $contactModel = $modelToUse::findFirst(
                [
                    'conditions'    => 'employee_id = :eid:',
                    'bind'          => [
                        'eid'       => $id
                    ]
                ]
            );

            if ($contactModel) {
                $contact = $contactModel->toArray();

                unset($contact['id']);

                $employee = array_merge($employee, $contact);

                $this->packagesData->responseCode = 0;

                $this->packagesData->employee = $employee;

                return true;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->employee = 'No Employee Found!';
    }
}