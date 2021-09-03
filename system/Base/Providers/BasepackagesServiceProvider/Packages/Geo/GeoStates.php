<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates as GeoStatesModel;
use System\Base\BasePackage;

class GeoStates extends BasePackage
{
    protected $modelToUse = GeoStatesModel::class;

    protected $packageName = 'geoStates';

    public $geoStates;

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addState(array $data)
    {
        if ($this->add($data)) {

            $this->updateSeq();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' state';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new state.';
        }
    }

    protected function updateSeq()
    {
        $totalStates = $this->modelToUse::find();

        if ($totalStates) {
            $lastStateId = (int) $totalStates->getLast()->id;

            if ($lastStateId > 100000) {
                return;
            }
        }

        $model = new $this->modelToUse;

        $table = $model->getSource();

        $sql = "UPDATE `{$table}` SET `id` = ? WHERE `{$table}`.`id` = ?";

        $this->db->execute($sql, [100001, $this->packagesData->last['id']]);
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function updateState(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' state';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating state.';
        }
    }

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