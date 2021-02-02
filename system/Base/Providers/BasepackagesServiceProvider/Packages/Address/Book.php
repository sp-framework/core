<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Address;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Address\BasepackagesAddressBook;

class Book extends BasePackage
{
    protected $modelToUse = BasepackagesAddressBook::class;

    protected $packageName = 'addressbook';

    public $addressbook;

    public function addAddress(array $data)
    {
        if ($data['city_id'] == 0 ||
            $data['state_id'] == 0 ||
            $data['country_id'] == 0
        ) {
            $data = $this->addNewGeoLocation($data);
        }

        if (!isset($data['address_type'])) {//Default is shipping address
            $data['address_type'] = 1;
        }

        if (!isset($data['is_primary'])) {//Default is primary address
            $data['is_primary'] = 1;
        }

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' address';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new address.';
        }
    }

    public function updateAddress(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' address';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating address.';
        }
    }

    public function removeAddress(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed address';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing address.';
        }
    }

    public function mergeAndUpdate(array $data)
    {
        $address = $this->getById($data['address_id']);

        unset($data['id']);

        $address = array_merge($address, $data);

        $this->updateAddress($address);

        return true;
    }

    protected function addNewGeoLocation($data)
    {
        //If Cities/States/Countries Id is received as 0, we create a new one and then assign proper ids.
        //New Id's can start from a large number to avoid any conflict.
        // if ($data['city_id'] == 0) {
        //     if ($data['country_id'] == 0) {
        //         $newCountry = [];//New Country
        //     }

        //     if ($data['state_id'] == 0) {
        //         $newState = [];//New state
        //     }

        //     $newCity = [];//New City
        // }

        return $data;
    }
}