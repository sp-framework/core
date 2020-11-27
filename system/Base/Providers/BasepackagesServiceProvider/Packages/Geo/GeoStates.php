<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\GeoStates as GeoStatesModel;
use System\Base\BasePackage;

class GeoStates extends BasePackage
{
    protected $modelToUse = GeoStatesModel::class;

    protected $packageName = 'geostates';

    public $geostates;

    public function searchStates(string $stateQueryString)
    {
        $searchStates =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :sName:',
                    'bind'          => [
                        'sName'     => '%' . $stateQueryString . '%'
                    ]
                ]
            );

        if ($searchStates) {
            $states = [];

            foreach ($searchStates as $stateKey => $stateValue) {
                $states[$stateKey] = $stateValue;
                $country = $this->basepackages->geoCountries->getById($stateValue['country_id']);
                $states[$stateKey]['country_id'] = $country['id'];
                $states[$stateKey]['country_name'] = $country['name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->states = $states;

            return true;
        }
    }
}