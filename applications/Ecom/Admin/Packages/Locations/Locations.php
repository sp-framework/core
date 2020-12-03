<?php

namespace Applications\Ecom\Admin\Packages\Locations;

use Applications\Ecom\Admin\Packages\Locations\Model\Locations as LocationsModel;
use System\Base\BasePackage;

class Locations extends BasePackage
{
    protected $modelToUse = LocationsModel::class;

    protected $packageName = 'locations';

    public $locations;

    public function addLocation(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' location';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new location.';
        }
    }

    public function updateLocation(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' location';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating location.';
        }
    }

    public function removeLocation(array $data)
    {
        //Check relations before removing.
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed location';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing location.';
        }
    }
}