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
        $this->setFFAddUsingUpdateOrInsert(true);

        if ($this->add($data)) {
            if ($this->config->databasetype !== 'db') {
                $this->ffStore->count(true);
            }

            $this->addResponse('Added ' . $data['name'] . ' city');
        } else {
            $this->addResponse('Error adding new city.', 1);
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

            if ((int) $lastDBId->fetch()['id'] < 100000) {
                return 100001;
            } else {
                return (int) $lastDBId->fetch()['id'] + 1;
            }
        } else {
            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->ffStore->count(true);

            $this->setFFAddUsingUpdateOrInsert(true);

            if ((int) $this->ffStore->getLastInsertedId() < 100000) {
                return 100001;
            } else {
                return (int) $this->ffStore->getLastInsertedId() + 1;
            }
        }
    }

    public function updateCity(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' city');
        } else {
            $this->addResponse('Error updating city.', 1);
        }
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

        if ($searchCities) {
            $cities = [];

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

            $this->packagesData->responseCode = 0;

            $this->packagesData->cities = $cities;

            return true;
        }

        return false;
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

        if ($searchPostCodes) {
            $postCodes = [];

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

            $this->packagesData->responseCode = 0;

            $this->packagesData->postCodes = $postCodes;

            return true;
        }

        return false;
    }
}