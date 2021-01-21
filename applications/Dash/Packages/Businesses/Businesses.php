<?php

namespace Applications\Dash\Packages\Businesses;

use Applications\Dash\Packages\Businesses\Model\Businesses as BusinessesModel;
use System\Base\BasePackage;

class Businesses extends BasePackage
{
    protected $modelToUse = BusinessesModel::class;

    protected $packageName = 'businesses';

    public $businesses;

    public function addBusiness(array $data)
    {
        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

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
        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->mergeAndUpdate($data);

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
        //Remove Address
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed business';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing business.';
        }
    }
}