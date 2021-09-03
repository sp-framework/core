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
        return $data;
        //Need to finish this.
        $foundCountry = null;

        if ($this->basepackages->geoCountries->searchCountries($address['Country'], true)) {
            $countryData = $this->basepackages->geoCountries->packagesData->countries;

            if (count($countryData) > 0) {
                foreach ($countryData as $countryKey => $country) {
                    if (strtolower($country['name']) === strtolower($address['Country'])) {
                        $foundCountry = $country;
                        $vendor['currency'] = $country['currency'];
                        break;
                    }
                }
            }
        }

        if (!$foundCountry) {
            $newCountry['name'] = $address['Country'];
            $newCountry['installed'] = '1';
            $newCountry['enabled'] = '1';
            $newCountry['user_added'] = '1';

            if ($this->basepackages->geoCountries->add($newCountry)) {
                $newAddress['country_id'] = $this->basepackages->geoCountries->packagesData->last['id'];
                $newAddress['country_name'] = $newCountry['name'];
            } else {

                $this->errors = array_merge($this->errors, ['Could not add country data.']);
            }
        } else {
            //We check if country is installed or not, if not, we install and enable it
            if ($foundCountry['installed'] != '1') {
                $foundCountry['enabled'] = '1';

                $this->basepackages->geoCountries->installCountry($foundCountry);
            } else if ($foundCountry['enabled'] != '1') {
                $foundCountry['enabled'] = '1';

                $this->basepackages->geoCountries->update($foundCountry);
            }

            $newAddress['country_id'] = $foundCountry['id'];
            $newAddress['country_name'] = $foundCountry['name'];
        }

        //State (Region in Xero Address)
        $foundState = null;

        if ($this->basepackages->geoStates->searchStatesByCode($address['Region'], true)) {
            $stateData = $this->basepackages->geoStates->packagesData->states;

            if (count($stateData) > 0) {
                foreach ($stateData as $stateKey => $state) {
                    if (strtolower($state['state_code']) === strtolower($address['Region'])) {
                        $foundState = $state;
                        break;
                    }
                }
            }
        }

        if (!$foundState) {
            $newState['name'] = $address['Region'];
            $newState['state_code'] = substr($address['Region'], 0, 3);
            $newState['user_added'] = '1';
            $newState['country_id'] = $newAddress['country_id'];

            if ($this->basepackages->geoStates->add($newState)) {
                $newAddress['state_id'] = $this->basepackages->geoStates->packagesData->last['id'];
                $newAddress['state_name'] = $newState['name'];
            } else {

                $this->errors = array_merge($this->errors, ['Could not add state data.']);
            }
        } else {
            $newAddress['state_id'] = $foundState['id'];
            $newAddress['state_name'] = $foundState['name'];
        }

        //New City
        $newCity['name'] = $address['City'];
        $newCity['state_id'] = $newAddress['state_id'];
        $newCity['country_id'] = $newAddress['country_id'];
        $newCity['user_added'] = '1';

        if ($this->basepackages->geoCities->add($newCity)) {
            $newAddress['city_id'] = $this->basepackages->geoCities->packagesData->last['id'];
            $newAddress['city_name'] = $newCity['name'];
        } else {

            $this->errors = array_merge($this->errors, ['Could not add city data.']);
        }

        return $data;
    }
}