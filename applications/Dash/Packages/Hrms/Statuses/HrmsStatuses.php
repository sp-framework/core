<?php

namespace Applications\Dash\Packages\Hrms\Statuses;

use Applications\Dash\Packages\Hrms\Statuses\Model\HrmsStatuses as HrmsStatusesModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class HrmsStatuses extends BasePackage
{
    protected $modelToUse = HrmsStatusesModel::class;

    protected $packageName = 'hrmsStatuses';

    public $hrmsStatuses;

    public function addStatus(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new status.';
        }
    }

    public function updateStatus(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating status.';
        }
    }

    public function removeStatus(array $data)
    {
        $status = $this->getById($id);

        if ($status['employees_count'] && (int) $status['employees_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Status is assigned to ' . $status['employees_count'] . ' employees. Error removing status.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed status';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing status.';
        }
    }
}