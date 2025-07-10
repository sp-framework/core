<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCities as GeoCitiesModel;
use System\Base\BasePackage;

class GeoCities extends BasePackage
{
    protected $modelToUse = GeoCitiesModel::class;

    protected $packageName = 'geoCities';

    public $geoCities;

    protected $countries = [];

    protected $states = [];

    public function addCity(array $data)
    {
        //
    }

    public function updateCity(array $data)
    {
        //
    }

    public function searchCities(string $cityQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchCities = $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName:',
                    'bind'          => [
                        'cName'     => '%' . $cityQueryString . '%'
                    ]
                ]
            );
        } else {
            $searchCities = $this->getByParams(['conditions' => ['name', 'LIKE', '%' . $cityQueryString . '%']]);
        }

        $cities = [];

        if ($searchCities) {
            foreach ($searchCities as $cityKey => $cityValue) {
                if (!isset($this->countries[$cityValue['country_id']])) {
                    $this->countries[$cityValue['country_id']] = $this->basepackages->geoCountries->getById($cityValue['country_id']);
                }

                if ($this->countries[$cityValue['country_id']]['enabled'] == 1 && $this->countries[$cityValue['country_id']]['installed'] == 1) {
                    $cities[$cityKey] = $cityValue;
                    if (!isset($this->states[$cityValue['state_id']])) {
                        $this->states[$cityValue['state_id']] = $this->basepackages->geoStates->getById($cityValue['state_id']);

                        if (!$this->states[$cityValue['state_id']]) {
                            continue;
                        }
                    }

                    $cities[$cityKey]['state_id'] = $this->states[$cityValue['state_id']]['id'];
                    $cities[$cityKey]['state_name'] = $this->states[$cityValue['state_id']]['name'];
                    $cities[$cityKey]['country_id'] = $this->countries[$cityValue['country_id']]['id'];
                    $cities[$cityKey]['country_name'] = $this->countries[$cityValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['cities' => $cities]);

        return $cities;
    }

    public function searchCitiesByCountryId($countryId)
    {
        if ($this->config->databasetype === 'db') {
            $searchCities =
                $this->getByParams(
                    [
                        'conditions'    => 'country_id = :countryId:',
                        'bind'          => [
                            'countryId'     => $countryId
                        ]
                    ]
                );
        } else {
            $searchCities = $this->getByParams(['conditions' => ['country_id', '=', (int) $countryId]]);
        }

        $this->addResponse('Ok', 0, ['cities' => $searchCities]);

        return $searchCities;
    }

    public function searchPostCodes(string $postCodeQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchPostCodes = $this->getByParams(
                [
                    'conditions'    => 'postcode LIKE :cPostCode:',
                    'bind'          => [
                        'cPostCode'     => '%' . $postCodeQueryString . '%'
                    ]
                ]
            );
        } else {
            $searchPostCodes = $this->getByParams(['conditions' => ['postcode', 'LIKE', '%' . $postCodeQueryString . '%']]);
        }

        $postCodes = [];

        if ($searchPostCodes) {
            foreach ($searchPostCodes as $postCodeKey => $postCodeValue) {
                if (!isset($this->countries[$cityValue['country_id']])) {
                    $this->countries[$cityValue['country_id']] = $this->basepackages->geoCountries->getById($postCodeValue['country_id']);
                }

                if ($this->countries[$cityValue['country_id']]['enabled'] == 1 && $this->countries[$cityValue['country_id']]['installed'] == 1) {
                    $postCodes[$postCodeKey] = $postCodeValue;
                    if (!isset($this->states[$postCodeValue['state_id']])) {
                        $this->states[$postCodeValue['state_id']] = $this->basepackages->geoStates->getById($postCodeValue['state_id']);

                        if (!$this->states[$postCodeValue['state_id']]) {
                            continue;
                        }
                    }

                    $postCodes[$postCodeKey]['state_id'] = $this->states[$postCodeValue['state_id']]['id'];
                    $postCodes[$postCodeKey]['state_name'] = $this->states[$postCodeValue['state_id']]['name'];
                    $postCodes[$postCodeKey]['country_id'] = $this->countries[$cityValue['country_id']]['id'];
                    $postCodes[$postCodeKey]['country_name'] = $this->countries[$cityValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['postCodes' => $postCodes]);

        return $postCodes;
    }
}