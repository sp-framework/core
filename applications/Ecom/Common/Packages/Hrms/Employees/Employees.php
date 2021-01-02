<?php

namespace Applications\Ecom\Common\Packages\Hrms\Employees;

use Applications\Ecom\Common\Packages\Hrms\Employees\Model\Employees as EmployeesModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Employees extends BasePackage
{
    protected $modelToUse = EmployeesModel::class;

    protected $packageName = 'employees';

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
}