<?php

namespace Applications\Dash\Packages\Hrms\Employees\Settings\Designations;

use Applications\Dash\Packages\Hrms\Employees\Settings\Designations\Model\HrmsEmployeesDesignations;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class EmployeesDesignations extends BasePackage
{
    protected $modelToUse = HrmsEmployeesDesignations::class;

    protected $packageName = 'employeesDesignations';

    public $employeesDesignations;

    public function addEmployeesDesignation(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' employee designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new employee designation.';
        }
    }

    public function updateEmployeesDesignation(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' employee designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating employee designation.';
        }
    }

    public function removeEmployeesDesignation(array $data)
    {
        $designation = $this->getById($id);

        if ($designation['employees_count'] && (int) $designation['employees_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Designation is assigned to ' . $designation['employees_count'] . ' employees. Error removing employee designation.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed employee designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing employee designation.';
        }
    }
}