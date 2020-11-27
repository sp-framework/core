<?php

namespace Applications\Admin\Packages\Businesses;

use Applications\Admin\Packages\Businesses\Model\Businesses as BusinessesModel;
use System\Base\BasePackage;

class Businesses extends BasePackage
{
    protected $modelToUse = BusinessesModel::class;

    protected $packageName = 'businesses';

    public $businesses;

    public function addBusiness(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' business';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new business.';
        }
    }

    public function updateBusiness(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' business';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating business.';
        }
    }

    public function removeBusiness(array $data)
    {
        //Check relations before removing.
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed business';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing business.';
        }
    }
}