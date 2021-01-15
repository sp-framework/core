<?php

namespace Applications\Dash\Packages\Hrms\Employees;

use Applications\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Employees extends BasePackage
{
    protected $modelToUse = HrmsEmployees::class;

    protected $packageName = 'hrmsemployees';

    public $employees;

    public function addEmployee(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' employee';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new employee.';
        }
    }

    public function updateEmployee(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' employee';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating employee.';
        }
    }

    public function removeEmployee(array $data)
    {
        $employee = $this->getById($id);

        if ($employee['product_count'] && (int) $employee['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Employee is assigned to ' . $employee['product_count'] . ' products. Error removing employee.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed employee';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing employee.';
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
}