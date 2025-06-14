<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates as GeoStatesModel;
use System\Base\BasePackage;

class GeoStates extends BasePackage
{
    protected $modelToUse = GeoStatesModel::class;

    protected $packageName = 'geoStates';

    public $geoStates;

    public function searchStates(string $stateQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchStates =
                $this->getByParams(
                    [
                        'conditions'    => 'name LIKE :sName:',
                        'bind'          => [
                            'sName'     => '%' . $stateQueryString . '%'
                        ]
                    ]
                );
        } else {
            $searchStates = $this->getByParams(['conditions' => ['name', 'LIKE', '%' . $stateQueryString . '%']]);
        }

        $states = [];

        if ($searchStates) {
            foreach ($searchStates as $stateKey => $stateValue) {
                $country = $this->basepackages->geoCountries->getById($stateValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $country['id'];
                    $states[$stateKey]['country_name'] = $country['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['states' => $states]);

        return $states;
    }

    public function searchStatesByCode(string $stateQueryString)
    {
        $searchStates =
            $this->getByParams(
                [
                    'conditions'    => 'state_code LIKE :sCode:',
                    'bind'          => [
                        'sCode'     => '%' . $stateQueryString . '%'
                    ]
                ]
            );

        $states = [];

        if ($searchStates) {
            foreach ($searchStates as $stateKey => $stateValue) {
                $country = $this->basepackages->geoCountries->getById($stateValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $country['id'];
                    $states[$stateKey]['country_name'] = $country['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['states' => $states]);

        return $states;
    }
}