<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;

class AddressBook extends BasePackage
{
    protected $modelToUse = BasepackagesAddressBook::class;

    protected $packageName = 'addressbook';

    public $addressbook;

    public function getAddressById($id)
    {
        $address = $this->getById($id);

        if ($address) {
            unset($address['id']);
            unset($address['name']);
            unset($address['address_type']);
            unset($address['is_primary']);
            unset($address['package_name']);

            return $address;
        }

        return false;
    }

    public function addAddress(array $data)
    {
        if (($data['city_id'] == 0 && $data['city_name'] !== '' ) ||
            ($data['state_id'] == 0 && $data['state_name'] !== '') ||
            ($data['country_id'] == 0 && $data['country_name'] !== '')
        ) {
            $data = $this->getGeoLocation($data);
        }

        if (!isset($data['address_type'])) {//Default is shipping address
            $data['address_type'] = 1;
        }

        if (!isset($data['is_primary'])) {//Default is primary address
            $data['is_primary'] = 1;
        }

        if ($this->add($data)) {
            $this->addResponse('Added address');
        } else {
            $this->addResponse('Error adding new address.', 1);
        }

        return $this->packagesData->last;
    }

    public function updateAddress(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated address');
        } else {
            $this->addResponse('Error updating address.', 1);
        }
    }

    public function removeAddress(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->addResponse('Removed address');
        } else {
            $this->addResponse('Error removing address.', 1);
        }
    }

    public function mergeAndUpdate(array $data)
    {
        $address = $this->getById($data['contact_address_id']);

        unset($data['id']);

        $address = array_merge($address, $data);

        $this->updateAddress($address);

        return true;
    }

    protected function getGeoLocation($data)
    {
        if ($this->basepackages->geoCities->searchCities($data['city_name'])) {
            $cityData = $this->basepackages->geoCities->packagesData->cities;

            if (count($cityData) > 0) {
                foreach ($cityData as $cityKey => $city) {
                    if (strtolower($city['name']) === strtolower($data['city_name'])) {
                        $data['city_id'] = $city['id'];
                        $data['city_name'] = $city['name'];
                        $data['state_id'] = $city['state_id'];
                        $data['state_name'] = $city['state_name'];
                        $data['country_id'] = $city['country_id'];
                        $data['country_name'] = $city['country_name'];

                        return $data;
                    }
                }
            }
        }

        //Country
        $foundCountry = null;

        if ($this->basepackages->geoCountries->searchCountries($data['country_name'], true)) {
            $countryData = $this->basepackages->geoCountries->packagesData->countries;

            if (count($countryData) > 0) {
                foreach ($countryData as $countryKey => $country) {
                    if (strtolower($country['name']) === strtolower($data['country_name'])) {
                        $foundCountry = $country;
                        break;
                    }
                }
            }
        }

        if (!$foundCountry) {
            $newCountry['name'] = $data['country_name'];
            $newCountry['installed'] = '1';
            $newCountry['enabled'] = '1';
            $newCountry['user_added'] = '1';

            if ($this->basepackages->geoCountries->addCountry($newCountry)) {
                $data['country_id'] = $this->basepackages->geoCountries->packagesData->last['id'];
                $data['country_name'] = $newCountry['name'];
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

            $data['country_id'] = $foundCountry['id'];
            $data['country_name'] = $foundCountry['name'];
        }

        //State
        $foundState = null;

        $stateData = null;

        if ($this->basepackages->geoStates->searchStates($data['state_name'], true)) {
            $stateData = $this->basepackages->geoStates->packagesData->states;
        }
        if (!$stateData && $this->basepackages->geoStates->searchStatesByCode($data['state_name'], true)) {
            $stateData = $this->basepackages->geoStates->packagesData->states;
        }

        if ($stateData && is_array($stateData) && count($stateData) > 0) {
            foreach ($stateData as $stateKey => $state) {
                if (strtolower($state['state_code']) === strtolower($data['state_name']) ||
                    strtolower($state['name']) === strtolower($data['state_name'])
                ) {
                    $foundState = $state;
                    break;
                }
            }
        }

        if (!$foundState) {
            $newState['name'] = $data['state_name'];
            $newState['state_code'] = strtoupper(substr($data['state_name'], 0, 3));
            $newState['user_added'] = '1';
            $newState['country_id'] = $data['country_id'];

            if ($this->basepackages->geoStates->addState($newState)) {
                $data['state_id'] = $this->basepackages->geoStates->packagesData->last['id'];
                $data['state_name'] = $newState['name'];
            }
        } else {
            $data['state_id'] = $foundState['id'];
            $data['state_name'] = $foundState['name'];
        }

        //New City
        $newCity['name'] = $data['city_name'];
        $newCity['state_id'] = $data['state_id'];
        $newCity['country_id'] = $data['country_id'];
        $newCity['user_added'] = '1';

        if ($this->basepackages->geoCities->addCity($newCity)) {
            $data['city_id'] = $this->basepackages->geoCities->packagesData->last['id'];
            $data['city_name'] = $newCity['name'];
        }

        return $data;
    }

    public function getAddressesTypes()
    {
        return
            [
                [
                    'id'              => '1',
                    'name'            => 'Shipping Address',
                    'status'          => '1',
                    'address_type'    => '1',
                    'description'     => 'Used for shipping packages.'
                ],
                [
                    'id'              => '2',
                    'name'            => 'Mailing Address',
                    'status'          => '1',
                    'address_type'    => '1',
                    'description'     => 'Used for mailing letters, invoices and bills, can be PO box.'
                ]
            ];
    }
}