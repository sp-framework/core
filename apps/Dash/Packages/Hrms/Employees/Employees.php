<?php

namespace Apps\Dash\Packages\Hrms\Employees;

use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
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
        $employeeObj = $this->getFirst('id', $id);

        if ($employeeObj) {
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

            $employee['address_ids'] = [];
            $employee['notes'] = [];
            $employee['activityLogs'] = [];

            if ($employeeObj->getAddresses()) {
                $employeeAddresses = $employeeObj->getAddresses()->toArray();

                if (count($employeeAddresses) > 0) {
                    foreach ($employeeAddresses as $employeeAddress) {
                        if (!isset($employee['address_ids'][$employeeAddress['address_type']])) {
                            $employee['address_ids'][$employeeAddress['address_type']] = [];
                        }

                        array_push($employee['address_ids'][$employeeAddress['address_type']], $employeeAddress);
                    }

                    foreach ($employee['address_ids'] as $addressTypeKey => $addressTypeAddresses) {
                        $employee['address_ids'][$addressTypeKey] =
                            msort($employee['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }
            }

            $employee['activityLogs'] = $this->getActivityLogs($employee['id']);

            $employee['notes'] = $this->basepackages->notes->getNotes('employees', $employee['id']);

            return $employee;
        }

        return false;
    }

    /**
     * @notification(name=add)
     */
    public function addEmployee(array $data)
    {
        if ($this->checkAccount($data['account_id'])) {
            $this->addResponse('Account ID (email) already in use.', 1);

            return;
        }

        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];


        if ($this->add($data)) {
            // if ($data['work_type_id'] == 2) {
                // $address = $data;

                // $address['package_row_id'] = $this->packagesData->last['id'];

                // $address['package_name'] = $this->packageName;

                // $this->basepackages->addressbook->addAddress($address);

                // $data['contact_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
            // }

            $data['id'] = $this->packagesData->last['id'];

            $this->updateAddresses($data);

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

    /**
     * @notification(name=update)
     */
    public function updateEmployee(array $data)
    {
        $employee = $this->getEmployeeById($data['id']);

        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        // if (isset($data['work_type_id']) && isset($data['contact_address_id'])) {

        // }

        if ($this->update($data)) {
        //     if ($data['work_type_id'] == 0 &&
        //         ($data['contact_address_id'] &&
        //          $data['contact_address_id'] != 0 &&
        //          $data['contact_address_id'] !== '')
        //     ) {
        //         $address = $data;

        //         $address['package_name'] = $this->packageName;

        //         $address['package_row_id'] = $data['id'];

                // $address['name'] = $data['full_name'];

            //     $address['address_id'] = $data['contact_address_id'];

            //     $this->basepackages->addressbook->mergeAndUpdate($address);

            // } else if ($data['work_type_id'] == 0 &&
            //            (!$data['contact_address_id'] ||
            //             $data['contact_address_id'] == 0 ||
            //             $data['contact_address_id'] === '')
            // ) {
            //     $address = $data;

            //     $address['package_name'] = $this->packageName;

            //     $address['package_row_id'] = $data['id'];

                // $address['name'] = $data['full_name'];

                // $address['id'] = '';

                // $this->basepackages->addressbook->addAddress($address);

                // $data['contact_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
            // }

            $data = $this->addRefId($data);

            $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);

            $data['id'] = $this->packagesData->last['id'];

            $this->updateAddresses($data);

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

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_mobile'] = $this->extractNumbers($data['contact_mobile']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

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

            $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
            $data['contact_mobile'] = $this->extractNumbers($data['contact_mobile']);
            $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

            $contact = $contactModel->toArray();

            $contact = array_merge($contact, $data);

            $this->update($contact);
        }

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function addEmployeeEmployment(array $data)
    {
        if ($data['employment_type_id'] == '2') {
            $data['contractor_vendor_id'] = Json::decode($data['contractor_vendor_id'], true);

            if ($data['contractor_vendor_id']['data'][0]) {
                $data['contractor_vendor_id'] = $data['contractor_vendor_id']['data'][0];
            } else if (isset($data['contractor_vendor_id']['newTags'][0])) {
                $data['contractor_vendor_id'] = $this->addContractorVendor($data['account_email'], $data['contractor_vendor_id']['newTags'][0]);
            }
        }

        $this->modelToUse = HrmsEmployeesEmployment::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = HrmsEmployees::class;
    }

    protected function updateEmployeeEmployment(array $data)
    {
        if ($data['employment_type_id'] == '2') {
            $data['contractor_vendor_id'] = Json::decode($data['contractor_vendor_id'], true);

            if (isset($data['contractor_vendor_id']['data'][0])) {
                $data['contractor_vendor_id'] = $data['contractor_vendor_id']['data'][0];
            } else if (isset($data['contractor_vendor_id']['newTags'][0])) {
                $data['contractor_vendor_id'] = $this->addContractorVendor($data['account_email'], $data['contractor_vendor_id']['newTags'][0]);
            }
        }

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

    protected function addContractorVendor($email, $vendorName)
    {
        $newVendor['abn'] = '0';
        $newVendor['business_name'] = $vendorName;
        $newVendor['contact_phone'] = '0';
        $newVendor['is_service_provider'] = '1';
        $newVendor['email'] = 'no-reply@' . $this->domains->domains[0]['name'];

        $vendorPackage = $this->usePackage(Vendors::class);

        $vendorPackage->addVendor($newVendor);

        $vendor = $vendorPackage->packagesData->last;

        $newNote['package_row_id'] = $vendor['vendor_id'];
        $newNote['note_type'] = '1';
        $newNote['note_app_visibility']['data'] = [];
        $newNote['is_private'] = '0';
        $newNote['note'] = 'Added via employee package : ' . $email;

        $this->basepackages->notes->addNote('vendors', $newNote);

        return $vendor['vendor_id'];
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

    /**
     * @notification(name=remove)
     */
    public function removeEmployee(array $data)
    {
        $employee = $this->getById($data['id']);

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $employee['portrait']);

            $this->basepackages->accounts->removeAccount(['id' => $employee['account_id']]);

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

            if ($data['status'] != '1') {//If not active, disable login
                $data['status'] = '0';
            }

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

            if ($data['status'] != '1') {//If not active, disable login
                $data['status'] = '0';
            }

            try {
                $this->basepackages->accounts->addAccount($data);

                return $this->basepackages->accounts->packagesData->packagesData['last']['id'];
            } catch (\Exception $e) {
                $this->addResponse('Error adding/updating employee account. Please contact administrator', 1);
            }
        }
    }

    protected function updateAddresses($data)
    {
        if (isset($data['address_ids']) && $data['address_ids'] !== '') {
            $data['address_ids'] = Json::decode($data['address_ids'], true);

            if (count($data['address_ids']) > 0) {
                foreach ($data['address_ids'] as $addressTypeKey => $addressType) {
                    $addressesIds[$addressTypeKey] = [];

                    if (is_array($addressType) && count($addressType) > 0) {
                        foreach ($addressType as $addressKey => $address) {

                            $address['address_type'] = $addressTypeKey;
                            $address['package_name'] = $this->packageName;
                            $address['package_row_id'] = $data['id'];

                            if ($address['seq'] == 0) {
                                $address['is_primary'] = 1;
                            } else {
                                $address['is_primary'] = 0;
                            }

                            if ($address['new'] == 1) {
                                $this->basepackages->addressbook->addAddress($address);
                            } else {
                                $address['id'] = $addressKey;
                                $this->basepackages->addressbook->updateAddress($address);
                            }
                        }
                    }
                }
            }
        }

        return true;
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