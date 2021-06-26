<?php

namespace Apps\Dash\Packages\Business\Locations;

use Apps\Dash\Packages\Business\Locations\Model\BusinessLocations;
use System\Base\BasePackage;

class Locations extends BasePackage
{
    protected $modelToUse = BusinessLocations::class;

    protected $packageName = 'locations';

    public $locations;

    public function addLocation(array $data)
    {
        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

        if ($this->add($data)) {
            $this->addActivityLog($data);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' location.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new location.';
        }
    }

    public function updateLocation(array $data)
    {
        $location = $this->getById($data['id']);

        $data['package_name'] = $this->packageName;

        $oldAddress = $this->basepackages->addressbook->getById($data['address_id']);

        $this->basepackages->addressbook->mergeAndUpdate($data);

        if ($this->update($data)) {
            $this->addActivityLog($data, $location, $oldAddress);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' location.';
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

        if ($location['total_employees'] && (int) $location['total_employees'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Location has ' . $location['total_employees'] . ' employees. Move employees to different location before removing location. Error removing location.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed location.';
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

    public function getLocationsByInboundShipping()
    {
        $this->getAll();

        $filter =
            $this->model->filter(
                function($location) {
                    $location = $location->toArray();

                    if ($location['inbound_shipping'] == 1) {
                        return $location;
                    }
                }
            );

        return $filter;
    }

    protected function addActivityLog(array $data, $oldData = null, $oldAddress = null)
    {
        if ($oldData && $oldAddress) {
            $oldData = array_merge($oldData, $oldAddress);
        }

        parent::addActivityLog($data, $oldData);
    }
}