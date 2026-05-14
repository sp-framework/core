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

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoCities', 'core')) {
                $this->geoCities = $this->opCache->getCache('geoCities', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoCities', $this->geoCities, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
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

    public function addCity(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('City added');

            return true;
        }

        $this->addResponse('Error Adding City', 1);
    }

    public function updateCity(array $data)
    {
        $city = $this->getById($data['id']);

        if (!$city) {
            $this->addResponse('City with ID does not exists', 1);

            return;
        }

        if ($this->update($data)) {
            $this->addResponse('City updated');

            return true;
        }

        $this->addResponse('Error Updating City', 1);
    }
}