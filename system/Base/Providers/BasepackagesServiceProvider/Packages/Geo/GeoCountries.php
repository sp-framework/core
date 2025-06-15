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

    public function updateCountry(array $data)
    {
        $country = $this->getById($data['id']);

        if ($country) {
            $country = array_merge($country, $data);

            if ($this->update($country)) {
                $this->addResponse('Updated country ' . $country['name']);
            } else {
                $this->addResponse('Error updating country ' . $country['name'], 1);
            }
        }
    }

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

        $countries = [];

        if ($searchCountries) {
            foreach ($searchCountries as $countryKey => $countryValue) {
                if ($all) {
                    $countries[$countryKey] = $countryValue;

                    continue;
                }

                if ($countryValue['enabled'] == 1 && $countryValue['installed'] == 1) {
                    $countries[$countryKey] = $countryValue;
                }
            }
        }

        $this->addResponse('Ok', 0, ['countries' => $countries]);

        return $countries;
    }

    public function installCountry(array $data)
    {
        if (!isset($data['country_iso2'])) {
            $this->addResponse('Please provide country in iso2 format', 1);

            return false;
        }

        if (!$this->downloadCountryData($data['country_iso2'])) {
            return false;
        }

        if (!$this->extractCountryData($data['country_iso2'])) {
            return false;
        }

        $countryData = $this->helper->decode($this->localContent->read($this->sourceDir . $data['country_iso2'] . '.json'), true);

        //Increase Exectimeout to 10 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 360) {
            set_time_limit(360);
        }

        $this->registerStates($countryData['states'], $countryData['id']);

        $country = $this->getById($data['country_id']);

        $country['installed'] = 1;

        if ($this->update($country)) {
            $this->addResponse('Installed country ' . $country['name']);
        } else {
            $this->addResponse('Error installing country ' . $country['name'], 1);
        }
    }

    public function uninstallCountry($data)
    {
        if (!isset($data['country_id'])) {
            $this->addResponse('Please provide country id', 1);

            return false;
        }

        $country = $this->getById($data['country_id']);

        //Remove States
        $statesData = $this->basepackages->geoStates->searchStatesByCountryId($country['id']);

        if ($statesData) {
            foreach ($statesData as $state) {
                $this->basepackages->geoStates->remove($state['id']);
            }
        }
        //Remove Cities
        $statesCities = $this->basepackages->geoCities->searchCitiesByCountryId($country['id']);

        if ($statesCities) {
            foreach ($statesCities as $city) {
                $this->basepackages->geoCities->remove($city['id']);
            }
        }

        //Delete files
        if ($this->localContent->fileExists($this->sourceDir . $country['iso2'] . '.json')) {
            $this->localContent->delete($this->sourceDir . $country['iso2'] . '.json');
        }
        if ($this->localContent->fileExists($this->sourceDir . $country['iso2'] . '.zip')) {
            $this->localContent->delete($this->sourceDir . $country['iso2'] . '.zip');
        }

        $country['installed'] = 0;

        if ($this->update($country)) {
            $this->addResponse('Uninstalled country ' . $country['name']);
        } else {
            $this->addResponse('Error uninstalling country ' . $country['name'], 1);
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
                        'https://github.com/sp-framework/geodata/raw/main/' . $country . '.zip',
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
                if (!$zip->extractTo(base_path($this->sourceDir))) {
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

    protected function registerStates($statesData, $country_id)
    {
        foreach ($statesData as $key => $state) {
            $state['country_id'] = $country_id;

            if (isset($state['cities'])) {
                $cities = $state['cities'];
                unset($state['cities']);
            }

            if (isset($state['id'])) {
                $this->basepackages->geoStates->setFFAddUsingUpdateOrInsert(true);

                $this->basepackages->geoStates->add($state);
            }

            if (isset($cities)) {
                $this->registerCities($cities, $country_id, $state['id']);
            }
        }
    }

    protected function registerCities($citiesData, $country_id, $state_id)
    {
        foreach ($citiesData as $key => $city) {
            $city['state_id'] = $state_id;
            $city['country_id'] = $country_id;

            if (isset($city['id'])) {
                $this->basepackages->geoCities->setFFAddUsingUpdateOrInsert(true);

                $this->basepackages->geoCities->add($city);
            }
        }
    }

    public function isEnabled($returnData = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'currency_enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ];
        } else {
            $conditions = ['conditions' => ['currency_enabled', '=', 1]];
        }

        $searchEnabledCountries = $this->getByParams($conditions);

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
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'currency_enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ];
        } else {
            $conditions = ['conditions' => ['currency_enabled', '=', 1]];
        }

        $searchEnabledCurrencies = $this->getByParams($conditions);

        if ($searchEnabledCurrencies) {
            if ($returnData) {
                return $searchEnabledCurrencies;
            }

            return true;
        }

        return [];
    }
}