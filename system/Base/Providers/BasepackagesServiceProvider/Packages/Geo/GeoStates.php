<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates as GeoStatesModel;
use System\Base\BasePackage;

class GeoStates extends BasePackage
{
    protected $modelToUse = GeoStatesModel::class;

    protected $packageName = 'geoStates';

    public $geoStates;

    protected $countries;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoStates', 'core')) {
                $this->geoStates = $this->opCache->getCache('geoStates', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoStates', $this->geoStates, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

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
                if (!isset($this->countries[$stateValue['country_id']])) {
                    $this->countries[$stateValue['country_id']] = $this->basepackages->geoCountries->getById($stateValue['country_id']);
                }

                if ($this->countries[$stateValue['country_id']]['enabled'] == 1 && $this->countries[$stateValue['country_id']]['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $this->countries[$stateValue['country_id']]['id'];
                    $states[$stateKey]['country_name'] = $this->countries[$stateValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['states' => $states]);

        return $states;
    }

    public function searchStatesByCode(string $stateQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchStates =
                $this->getByParams(
                    [
                        'conditions'    => 'state_code LIKE :sCode:',
                        'bind'          => [
                            'sCode'     => '%' . $stateQueryString . '%'
                        ]
                    ]
                );
        } else {
            $searchStates = $this->getByParams(['conditions' => ['state_code', 'LIKE', '%' . $stateQueryString . '%']]);
        }

        $states = [];

        if ($searchStates) {
            foreach ($searchStates as $stateKey => $stateValue) {
                if (!isset($this->countries[$stateValue['country_id']])) {
                    $this->countries[$stateValue['country_id']] = $this->basepackages->geoCountries->getById($stateValue['country_id']);
                }

                if ($this->countries[$stateValue['country_id']]['enabled'] == 1 && $this->countries[$stateValue['country_id']]['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $this->countries[$stateValue['country_id']]['id'];
                    $states[$stateKey]['country_name'] = $this->countries[$stateValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['states' => $states]);

        return $states;
    }

    public function searchStatesByCountryId($countryId)
    {
        if ($this->config->databasetype === 'db') {
            $searchStates =
                $this->getByParams(
                    [
                        'conditions'    => 'country_id = :countryId:',
                        'bind'          => [
                            'countryId'     => $countryId
                        ]
                    ]
                );
        } else {
            $searchStates = $this->getByParams(['conditions' => ['country_id', '=', (int) $countryId]]);
        }

        $this->addResponse('Ok', 0, ['states' => $searchStates]);

        return $searchStates;
    }
}