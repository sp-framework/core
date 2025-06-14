<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCities as GeoCitiesModel;
use System\Base\BasePackage;

class GeoCities extends BasePackage
{
    protected $modelToUse = GeoCitiesModel::class;

    protected $packageName = 'geoCities';

    public $geoCities;

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
                $country = $this->basepackages->geoCountries->getById($cityValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $cities[$cityKey] = $cityValue;
                    $state = $this->basepackages->geoStates->getById($cityValue['state_id']);
                    $cities[$cityKey]['state_id'] = $state['id'];
                    $cities[$cityKey]['state_name'] = $state['name'];
                    $cities[$cityKey]['country_id'] = $country['id'];
                    $cities[$cityKey]['country_name'] = $country['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['cities' => $cities]);

        return $cities;
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
                $country = $this->basepackages->geoCountries->getById($postCodeValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $postCodes[$postCodeKey] = $postCodeValue;
                    $state = $this->basepackages->geoStates->getById($postCodeValue['state_id']);
                    $postCodes[$postCodeKey]['state_id'] = $state['id'];
                    $postCodes[$postCodeKey]['state_name'] = $state['name'];
                    $postCodes[$postCodeKey]['country_id'] = $country['id'];
                    $postCodes[$postCodeKey]['country_name'] = $country['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['postCodes' => $postCodes]);

        return $postCodes;
    }
}