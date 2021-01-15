<?php

namespace Applications\Dash\Packages\Locations;

use Applications\Dash\Packages\Locations\Model\Locations as LocationsModel;
use System\Base\BasePackage;

class Locations extends BasePackage
{
    protected $modelToUse = LocationsModel::class;

    protected $packageName = 'locations';

    public $locations;

    public function addLocation(array $data)
    {
        $data['type'] = 'locations';

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

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
        $data['type'] = 'locations';

        $address = $this->basepackages->addressbook->getById($data['address_id']);

        $address = array_merge($address, $data);

        $this->basepackages->addressbook->updateAddress($data);

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
        $location = $this->getById($data['id']);

        if ($location['total_stock_qty'] && (int) $location['total_stock_qty'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Location carries stock of ' . $location['total_stock_qty'] . ' products. Move stock to different location before removing location. Error removing location.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed location';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing location.';
        }
    }

    protected function addStockQty()
    {
        //
    }

    protected function removeStockQty()
    {
        //
    }

    public function getLocationById($data)
    {
        $location = $this->getById($data['id']);

        $this->packagesData->locationAddress =
            $this->basepackages->addressbook->getById($location['address_id']);

        return true;
    }
}