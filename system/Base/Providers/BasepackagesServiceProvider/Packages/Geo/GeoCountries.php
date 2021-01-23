<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\GeoCountries as GeoCountriesModel;

class GeoCountries extends BasePackage
{
    protected $modelToUse = GeoCountriesModel::class;

    protected $packageName = 'geoCountries';

    public $geoCountries;

    public function searchCountries(string $countryQueryString)
    {
        $searchCountries =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName:',
                    'bind'          => [
                        'cName'     => '%' . $countryQueryString . '%'
                    ]
                ]
            );

        if ($searchCountries) {
            $countries = [];

            foreach ($searchCountries as $countryKey => $countryValue) {
                $country = $this->getById($countryValue['id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $countries[$countryKey] = $countryValue;
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->countries = $countries;

            return true;
        }
    }

    public function addCountry(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' country';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new country.';
        }
    }

    public function updateCountry(array $data)
    {
        $country = $this->getById($data['id']);

        $country = array_merge($country, $data);

        if ($this->update($country)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' country';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating country.';
        }
    }

    public function installCountry(array $data)
    {
        $countryData =
            Json::decode(
                $this->localContent->read(
                    '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/' . $data['country_iso3'] . '.json'
                ),
                true
            );

        $this->registerStates($countryData['states'], $countryData['id']);
            // dump($countryData);
        $this->registerTimezones($countryData['timezones'], $countryData['id']);

        $country = $this->getById($data['country_id']);

        $country['installed'] = 1;

        if ($this->update($country)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Installed ' . $country['name'] . ' country';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error installing ' . $country['name'] . ' country.';
        }
    }

    protected function registerTimezones($timezonesData, $country_id)
    {
        foreach ($timezonesData as $key => $timezone) {
            $geoTimezone = [];
            $geoTimezone['country_id'] = $country_id;
            $geoTimezone['zone_name'] = $timezone['zoneName'];
            $geoTimezone['gmt_offset'] = $timezone['gmtOffset'];
            $geoTimezone['gmt_offset_name'] = $timezone['gmtOffsetName'];
            $geoTimezone['abbreviation'] = $timezone['abbreviation'];
            $geoTimezone['tz_name'] = $timezone['tzName'];

            $this->basepackages->geoTimezones->add($geoTimezone);
        }
    }

    protected function registerStates($statesData, $country_id)
    {
        foreach ($statesData as $key => $state) {
            $state['country_id'] = $country_id;
            $this->basepackages->geoStates->add($state);
            // dump($state);
            // $this->db->insertAsDict(
            //     'geo_states',
            //     [
            //         'id'            => $statesData[$key]['id'],
            //         'name'          => $statesData[$key]['name'],
            //         'state_code'    => $statesData[$key]['state_code'],
            //         'country_id'    => $country_id
            //     ]
            // );

            $this->registerCities($statesData[$key]['cities'], $country_id, $statesData[$key]['id']);
        }
    }

    protected function registerCities($citiesData, $country_id, $state_id)
    {
        foreach ($citiesData as $key => $city) {
            $city['state_id'] = $state_id;
            $city['country_id'] = $country_id;
            $this->basepackages->geoCities->add($city);
            // var_dump($city);
            // die();
            // $this->db->insertAsDict(
            //     'geo_cities',
            //     [
            //         'id'            => $citiesData[$key]['id'],
            //         'name'          => $citiesData[$key]['name'],
            //         'latitude'      => $citiesData[$key]['latitude'],
            //         'longitude'     => $citiesData[$key]['longitude'],
            //         'state_id'      => $state_id,
            //         'country_id'    => $country_id
            //     ]
            // );
        }
    }

    public function isEnabled()
    {
        $searchEnabledCountries =
            $this->getByParams(
                [
                    'conditions'    => 'enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ]
            );

        if ($searchEnabledCountries) {
            return true;
        }
    }
}