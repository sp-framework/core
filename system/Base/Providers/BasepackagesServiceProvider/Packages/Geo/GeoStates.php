<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates as GeoStatesModel;
use System\Base\BasePackage;

class GeoStates extends BasePackage
{
    protected $modelToUse = GeoStatesModel::class;

    protected $packageName = 'geoStates';

    public $geoStates;

    public function addState(array $data)
    {
        if ($this->add($data)) {

            if (!isset($data['id'])) {
                if ($this->config->databasetype === 'db') {
                    $this->updateSeq();
                }
            }

            $this->addResponse('Added ' . $data['name'] . ' state');
        } else {
            $this->addResponse('Error adding new state.', 1);
        }
    }

    protected function updateSeq()
    {
        $lastStateId = $this->modelToUse::maximum(['column' => 'id']);

        if ($lastStateId && (int) $lastStateId > 100000) {
            return;
        }

        $model = new $this->modelToUse;

        $table = $model->getSource();

        $sql = "UPDATE `{$table}` SET `id` = ? WHERE `{$table}`.`id` = ?";

        $this->db->execute($sql, [100001, $this->packagesData->last['id']]);
    }

    public function updateState(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' state');
        } else {
            $this->addResponse('Error updating state.', 1);
        }
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

        if ($searchStates) {
            $states = [];

            foreach ($searchStates as $stateKey => $stateValue) {
                $country = $this->basepackages->geoCountries->getById($stateValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $country['id'];
                    $states[$stateKey]['country_name'] = $country['name'];
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->states = $states;

            return true;
        }
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

        if ($searchStates) {
            $states = [];

            foreach ($searchStates as $stateKey => $stateValue) {
                $country = $this->basepackages->geoCountries->getById($stateValue['country_id']);

                if ($country['enabled'] == 1 && $country['installed'] == 1) {
                    $states[$stateKey] = $stateValue;
                    $states[$stateKey]['country_id'] = $country['id'];
                    $states[$stateKey]['country_name'] = $country['name'];
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->states = $states;

            return true;
        }
    }
}