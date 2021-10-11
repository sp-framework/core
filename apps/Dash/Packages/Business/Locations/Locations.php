<?php

namespace Apps\Dash\Packages\Business\Locations;

use Apps\Dash\Packages\Business\Locations\Model\BusinessLocations;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Locations extends BasePackage
{
    protected $modelToUse = BusinessLocations::class;

    protected $packageName = 'locations';

    public $locations;

    public function addLocation(array $data)
    {
        $data = $this->updateEmployees($data);

        if ($this->add($data)) {
            $data['package_name'] = $this->packageName;
            $data['package_row_id'] = $this->packagesData->last['id'];
            $this->basepackages->addressbook->addAddress($data);

            $this->addActivityLog($data);

            $this->addResponse('Added ' . $data['name'] . ' location.');
        } else {
            $this->addResponse('Error adding new location.', 1);
        }
    }

    public function updateLocation(array $data)
    {
        $location = $this->getById($data['id']);

        $data = $this->updateEmployees($data);

        $oldAddress = $this->basepackages->addressbook->getById($data['address_id']);

        if ($this->update($data)) {
            $data['package_name'] = $this->packageName;
            $this->basepackages->addressbook->mergeAndUpdate($data);

            $this->addActivityLog($data, $location, $oldAddress);

            $this->addResponse('Updated ' . $data['name'] . ' location.');
        } else {
            $this->addResponse('Error updating location.', 1);
        }
    }

    public function removeLocation(array $data)
    {
        $location = $this->getById($data['id']);

        if ($location['total_stock_qty'] && (int) $location['total_stock_qty'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Location carries stock of ' .
                $location['total_stock_qty'] .
                ' products. Move stock to different location before removing location. Error removing location.';

            return false;
        }

        if ($location['total_employees'] && (int) $location['total_employees'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Location has ' .
                $location['total_employees'] .
                ' employees. Move employees to different location before removing location. Error removing location.';

            return false;
        }

        $addressObj = $this->getLocationAddress($data['id'], true);

        if ($this->remove($data['id'])) {
            if ($addressObj) {
                $addressObj->delete();
            }

            $this->addResponse('Removed location.');
        } else {
            $this->addResponse('Error removing location.', 1);
        }
    }

    protected function updateEmployees($data)
    {
        if ($data['employee_ids'] !== '') {
            $data['employee_ids'] = Json::decode($data['employee_ids'], true);

            $employeesIds = [];
            if (count($data['employee_ids']) > 0) {
                $employees = $this->usePackage(Employees::class);

                $data['employee_ids'] = msort($data['employee_ids'], 'seq');

                foreach ($data['employee_ids'] as $employeeKey => $employee) {
                    array_push($employeesIds, $employee['id']);
                }
            }
        }

        $data['employee_ids'] = Json::encode($employeesIds);

        return $data;
    }

    protected function addStockQty()
    {
        //
    }

    protected function removeStockQty()
    {
        //
    }

    public function getLocationById($id)
    {
        $location = $this->getById($id);

        if ($location['employee_ids'] && $location['employee_ids'] !== '') {
            $location['employee_ids'] = Json::decode($location['employee_ids'], true);

            foreach ($location['employee_ids'] as $employeeKey => $employee) {
                if ($this->employees->searchById($employee)) {
                    $employeeArr = $this->employees->packagesData->employee;

                    $location['employee_ids'][$employeeKey] = $employeeArr;
                } else {
                    unset($location['employee_ids'][$employeeKey]);
                }
            }
        }

        // unset($location['employee_ids']);

        $location['address'] = $this->getLocationAddress($id);
            // $this->basepackages->addressbook->getById($location['address_id']);

        // unset($location['address_id']);

        $this->addResponse('Ok', 0, $location);

        return $location;
    }

    public function getLocationsByEntityId($data)
    {
        $this->getAll();

        $filter =
            $this->model->filter(
                function($location) use ($data) {
                    $location = $location->toArray();

                    if ($location['entity_id'] == $data['entity_id']) {

                        return $location;
                    }
                }
            );

        return $filter;
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

    protected function getLocationAddress($id, $obj = false)
    {
        $locationObj = $this->getFirst('id', $id);

        if ($locationObj) {
            $addressObj = $locationObj->getAddress();

            if ($addressObj) {
                if ($obj) {
                    return $addressObj;
                }

                return $addressObj->toArray();
            }
        }

        return [];
    }
}