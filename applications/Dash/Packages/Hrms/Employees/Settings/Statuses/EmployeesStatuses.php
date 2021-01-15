<?php

namespace Applications\Dash\Packages\Hrms\Employees\Settings\Statuses;

use Applications\Dash\Packages\Hrms\Employees\Settings\Statuses\Model\HrmsEmployeesStatuses;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class EmployeesStatuses extends BasePackage
{
    protected $modelToUse = HrmsEmployeesStatuses::class;

    protected $packageName = 'employeesStatuses';

    public $employeesStatuses;

    public function addEmployeesStatus(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' employee status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new employee status.';
        }
    }

    public function updateEmployeesStatus(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' employee status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating employee status.';
        }
    }

    public function removeEmployeesStatus(array $data)
    {
        $status = $this->getById($id);

        if ($status['employees_count'] && (int) $status['employees_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Status is assigned to ' . $status['employees_count'] . ' employees. Error removing employee status.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed employee status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing employee status.';
        }
    }
}