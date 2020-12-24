<?php

namespace Applications\Ecom\Admin\Packages\Locations\Settings\Types;

use Applications\Ecom\Admin\Packages\Locations\Settings\Types\Model\LocationsTypes as LocationsTypesModel;
use System\Base\BasePackage;

class LocationsTypes extends BasePackage
{
    protected $modelToUse = LocationsTypesModel::class;

    protected $packageName = 'locationstypes';

    public $locationstypes;

    public function addLocationsType(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' location type';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new location type.';
        }
    }

    public function updateLocationsType(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' location type';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating location type.';
        }
    }

    public function removeLocationsType(array $data)
    {
        //Check relations before removing.
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed location type';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing location type.';
        }
    }
}