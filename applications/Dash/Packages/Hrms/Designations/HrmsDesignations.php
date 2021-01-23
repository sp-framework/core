<?php

namespace Applications\Dash\Packages\Hrms\Designations;

use Applications\Dash\Packages\Hrms\Designations\Model\HrmsDesignations as HrmsDesignationsModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class HrmsDesignations extends BasePackage
{
    protected $modelToUse = HrmsDesignationsModel::class;

    protected $packageName = 'hrmsDesignations';

    public $hrmsDesignations;

    public function addDesignation(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new designation.';
        }
    }

    public function updateDesignation(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating designation.';
        }
    }

    public function removeDesignation(array $data)
    {
        $designation = $this->getById($id);

        if ($designation['employees_count'] && (int) $designation['employees_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Designation is assigned to ' . $designation['employees_count'] . ' employees. Error removing designation.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed designation';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing designation.';
        }
    }
}