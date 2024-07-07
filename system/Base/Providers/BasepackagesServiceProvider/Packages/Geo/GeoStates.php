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
        $data['id'] = $this->getNextIdFromDB();

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

    protected function getNextIdFromDB()
    {
        if ($this->config->databasetype === 'db') {
            $model = new $this->modelToUse;
            $table = $model->getSource();
            $sql = "SELECT id FROM {$table} ORDER BY id DESC LIMIT 1";

            $lastDBId = $this->executeSql($sql);
            $lastDBId->setFetchMode(\Phalcon\Db\Enum::FETCH_ASSOC);

            if ((int) $lastDBId->fetch()['id'] < 1000) {
                return 1001;
            } else {
                return (int) $lastDBId->fetch()['id'] + 1;
            }
        } else {
            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->ffStore->count(true);

            $this->setFFAddUsingUpdateOrInsert(true);

            if ((int) $this->ffStore->getLastInsertedId() < 10000) {
                return 10001;
            } else {
                return (int) $this->ffStore->getLastInsertedId() + 1;
            }
        }
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