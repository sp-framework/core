<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\GeoCities as GeoCitiesModel;
use System\Base\BasePackage;

class GeoCities extends BasePackage
{
    protected $modelToUse = GeoCitiesModel::class;

    protected $packageName = 'geocities';

    public $geocities;

    public function searchCities(string $cityQueryString)
    {
        $searchCities =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName:',
                    'bind'          => [
                        'cName'     => '%' . $cityQueryString . '%'
                    ]
                ]
            );

        if ($searchCities) {
            $cities = [];

            foreach ($searchCities as $cityKey => $cityValue) {
                $cities[$cityKey] = $cityValue;
                $state = $this->basepackages->geoStates->getById($cityValue['state_id']);
                $cities[$cityKey]['state_id'] = $state['id'];
                $cities[$cityKey]['state_name'] = $state['name'];
                $country = $this->basepackages->geoCountries->getById($cityValue['country_id']);
                $cities[$cityKey]['country_id'] = $country['id'];
                $cities[$cityKey]['country_name'] = $country['name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->cities = $cities;

            return true;
        }
    }
}