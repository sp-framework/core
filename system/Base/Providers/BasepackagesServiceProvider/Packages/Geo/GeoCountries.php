<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCountries as GeoCountriesModel;

class GeoCountries extends BasePackage
{
    protected $modelToUse = GeoCountriesModel::class;

    protected $packageName = 'geoCountries';

    public $geoCountries;

    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    public function searchCountries(string $countryQueryString, $all = false)
    {
        if ($this->config->databasetype === 'db') {
            $searchCountries =
                $this->getByParams(
                    [
                        'conditions'    => 'name LIKE :cName:',
                        'bind'          => [
                            'cName'     => '%' . $countryQueryString . '%'
                        ]
                    ]
                );
        } else {
            $searchCountries = $this->getByParams(['conditions' => ['name', 'LIKE', '%' . $countryQueryString . '%']]);
        }

        if ($searchCountries) {
            $countries = [];

            foreach ($searchCountries as $countryKey => $countryValue) {
                // $country = $this->getById($countryValue['id']);

                if ($all) {
                    $countries[$countryKey] = $countryValue;
                    continue;
                }

                if ($countryValue['enabled'] == 1 && $countryValue['installed'] == 1) {
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
        if (!isset($data['installed'])) {
            $data['installed'] = '1';
        }

        if ($this->add($data)) {

            if (!isset($data['id'])) {
                if ($this->config->databasetype === 'db') {
                    $this->updateSeq();
                }
            }

            $this->addResponse('Added country ' . $data['name']);
        } else {
            $this->addResponse('Error adding country ' . $data['name'], 1);
        }
    }

    protected function updateSeq()
    {
        $lastCountryId = $this->modelToUse::maximum(['column' => 'id']);

        if ($lastCountryId && (int) $lastCountryId > 1000) {
            return;
        }

        $model = new $this->modelToUse;

        $table = $model->getSource();

        $sql = "UPDATE `{$table}` SET `id` = ? WHERE `{$table}`.`id` = ?";

        $this->db->execute($sql, [1001, $this->packagesData->last['id']]);
    }

    public function updateCountry(array $data)
    {
        $country = $this->getById($data['id']);

        $country = array_merge($country, $data);

        if ($this->update($country)) {
            $this->addResponse('Updated country ' . $data['name']);
        } else {
            $this->addResponse('Error updating country ' . $country['name'], 1);
        }
    }

    public function installCountry(array $data)
    {
        if (!$this->downloadCountryData($data['country_iso2'])) {
            return false;
        }

        if (!$this->extractCountryData($data['country_iso2'])) {
            return false;
        }

        $countryData = $this->helper->decode($this->localContent->read($this->sourceDir . $data['country_iso2'] . '.json'), true);

        $this->registerStates($countryData['states'], $countryData['id']);
            // dump($countryData);
        // $this->registerTimezones($countryData['timezones'], $countryData['id']);

        $country = $this->getById($data['country_id']);

        $country['installed'] = 1;

        if ($this->update($country)) {
            $this->addResponse('Installed country ' . $country['name']);
        } else {
            $this->addResponse('Error installing country ' . $country['name'], 1);
        }
    }

    protected function downloadCountryData($country)
    {
        try {
            $this->localContent->write(
                $this->sourceDir . $country . '.zip',
                $this->remoteWebContent
                    ->request(
                        'GET',
                        'https://dev.bazaari.com.au/sp-public/geodata/raw/branch/master/' . $country . '.zip',
                        ['verify' => false]
                    )->getBody()->getContents()
                );
            return true;
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return false;
    }

    protected function extractCountryData($country)
    {
        $zip = new \ZipArchive;

        try {
            if ($zip->open(base_path($this->sourceDir . $country . '.zip'))) {
                if (!$zip->extractTo('/')) {
                    $this->addResponse('Country zip file corrupt.', 1);

                    return false;
                }
            } else {
                $this->addResponse('Country zip file corrupt.', 1);

                return false;
            }

            $zip->close();

            return true;
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }
    }

    // protected function registerTimezones($timezonesData, $country_id)
    // {
    //     foreach ($timezonesData as $key => $timezone) {
    //         $geoTimezone = [];
    //         $geoTimezone['country_id'] = $country_id;
    //         $geoTimezone['zone_name'] = $timezone['zoneName'];
    //         $geoTimezone['gmt_offset'] = $timezone['gmtOffset'];
    //         $geoTimezone['gmt_offset_name'] = $timezone['gmtOffsetName'];
    //         $geoTimezone['abbreviation'] = $timezone['abbreviation'];
    //         $geoTimezone['tz_name'] = $timezone['tzName'];

    //         $this->basepackages->geoTimezones->add($geoTimezone);
    //     }
    // }

    protected function registerStates($statesData, $country_id)
    {
        $searchByCities = [];

        foreach ($statesData as $key => $state) {
            $state['country_id'] = $country_id;

            if (isset($state['cities'])) {
                $cities = $state['cities'];
                unset($state['cities']);
            }

            if (isset($state['id']) && $this->basepackages->geoStates->getById($state['id'])) {
                $this->basepackages->geoStates->updateState($state);
            } else if (count($state) === 2 && isset($state['name'])) {//We make sure we have only state['name'] && state['cities'] set
                $searchByCities[$key] = $state;

                if (isset($cities)) {
                    $searchByCities[$key]['cities'] = $cities;
                }

                continue;
            } else {
                $this->basepackages->geoStates->addState($state);
            }

            if (isset($cities)) {
                $this->registerCities($cities, $country_id, $state['id']);
            }
        }

        if (count($searchByCities) > 0) {
            $this->searchByCities($searchByCities, $country_id);
        }
    }

    protected function registerCities($citiesData, $country_id, $state_id)
    {
        foreach ($citiesData as $key => $city) {
            $city['state_id'] = $state_id;
            $city['country_id'] = $country_id;

            if (isset($city['id']) && $this->basepackages->geoCities->getById($city['id'])) {
                $this->basepackages->geoCities->updateCity($city);
            } else {
                $this->basepackages->geoCities->addCity($city);
            }
        }
    }

    protected function searchByCities($searchByCities, $country_id)
    {
        foreach ($searchByCities as $stateKey => $state) {
            if (count($state['cities']) > 0) {
                foreach ($state['cities'] as $cityKey => $city) {
                    $dbCityObj = $this->basepackages->geoCities->getFirst('name', $city['name']);

                    if ($dbCityObj) {
                        $dbCity = $dbCityObj->toArray();

                        $dbCity = array_merge($dbCity, $city);

                        $this->basepackages->geoCities->updateCity($dbCity);
                    } else {
                        $city['state_id'] = 0;
                        $city['country_id'] = $country_id;

                        $this->basepackages->geoCities->addCity($city);
                    }
                }
            }
        }
    }

    public function isEnabled($returnData = false)
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
            if ($returnData) {
                return $searchEnabledCountries;
            }

            return true;
        }

        return [];
    }

    public function currencyEnabled($returnData = false)
    {
        $searchEnabledCurrencies =
            $this->getByParams(
                [
                    'conditions'    => 'currency_enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ]
            );

        if ($searchEnabledCurrencies) {
            if ($returnData) {
                return $searchEnabledCurrencies;
            }

            return true;
        }

        return [];
    }
}