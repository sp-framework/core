<?php

namespace Apps\Dash\Packages\Hrms\Employees;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Employees extends BasePackage
{
    protected $modelToUse = HrmsEmployees::class;

    protected $packageName = 'employees';

    public $employees;

    public function addEmployee(array $data)
    {
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
            $this->basepackages->storages->changeOrphanStatus($data['portrait']);

            if ($data['employment_attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['employment_attachments'], null, true);
            }

            $data['id'] = $this->packagesData->last['id'];

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Added ' . $data['full_name'] . ' employee');
        } else {
            $this->addResponse('Error adding new employee.', 1);
        }
    }

    public function updateEmployee(array $data)
    {
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

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

        $employee = $this->getById($data['id']);

        if ($this->update($data)) {
            $this->basepackages->storages->changeOrphanStatus($data['portrait'], $employee['portrait']);

            if ($data['employment_attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['employment_attachments'], $employee['employment_attachments'], true);
            }

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Updated ' . $data['full_name'] . ' employee');
        } else {
            $this->addResponse('Error updating employee.', 1);
        }
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
}