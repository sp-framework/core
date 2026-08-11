<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use Apps\Core\Components\System\Tools\Dataextractors\DataextractorsComponent;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCountries as GeoCountriesModel;

class GeoCountries extends BasePackage
{
    protected $modelToUse = GeoCountriesModel::class;

    protected $packageName = 'geoCountries';

    public $geoCountries;

    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/';

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoCountries', 'core')) {
                $this->geoCountries = $this->opCache->getCache('geoCountries', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoCountries', $this->geoCountries, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

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

        $dataExtractorComponent = (new DataextractorsComponent)->initialize();

        $dataExtractorComponent->processAction(['process' => 'geo', 'countries' => [$data['country_iso2']]]);

        $this->addResponse(
            $dataExtractorComponent->dataExtractors->packagesData->responseMessage,
            $dataExtractorComponent->dataExtractors->packagesData->responseCode,
            $dataExtractorComponent->dataExtractors->packagesData->responseData ?? []
        );
    }

    public function uninstallCountry($data)
    {
        // /etc/apache2.conf - Change the timeout to 3600 else you will get Gateway Timeout, revert back when done to 300 (5 mins)
        // Timeout 3600

        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 3600) {
            set_time_limit(3600);
        }

        //Increase memory_limit to 2G as the process takes a bit of memory to process the array.
        if ((int) ini_get('memory_limit') < 2048) {
            ini_set('memory_limit', '2048M');
        }

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

        //Remove Postcodes
        $statesPostcodes = $this->basepackages->geoPostcodes->searchPostcodesByCountryId($country['id']);

        if ($statesPostcodes) {
            foreach ($statesPostcodes as $postcode) {
                $this->basepackages->geoPostcodes->remove($postcode['id']);
            }
        }

        //Delete files
        if ($this->localContent->fileExists($this->sourceDir . $country['iso2'] . '.json')) {
            $this->localContent->delete($this->sourceDir . $country['iso2'] . '.json');
        }

        $country['installed'] = 0;
        $country['enabled'] = 0;
        $country['currency_enabled'] = 0;

        if ($this->update($country)) {
            $this->addResponse('Uninstalled country ' . $country['name']);
        } else {
            $this->addResponse('Error uninstalling country ' . $country['name'], 1);
        }
    }

    public function isEnabled($countryId = null, $returnData = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ];

            if ($countryId) {
                $conditions['conditions'] = 'enabled = :cEnabled: AND id = :cId:';
                $conditions['bind']['cId'] = $countryId;
            }
        } else {
            $conditions = ['conditions' => ['enabled', '=', 1]];

            if ($countryId) {
                $conditions['conditions'] = [$conditions['conditions']];
                array_push($conditions['conditions'], ['id', '=', (int) $countryId]);
            }
        }

        $searchEnabledCountries = $this->getByParams($conditions);

        if ($searchEnabledCountries) {
            if ($returnData) {
                return $searchEnabledCountries;
            }

            if ($countryId) {
                return true;
            }
        }

        if ($countryId) {
            return false;
        }

        return [];
    }
}