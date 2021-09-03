<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCities as GeoCitiesModel;
use System\Base\BasePackage;

class GeoCities extends BasePackage
{
    protected $modelToUse = GeoCitiesModel::class;

    protected $packageName = 'geoCities';

    public $geoCities;

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addCity(array $data)
    {
        if ($this->add($data)) {

            $this->updateSeq();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' city';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new city.';
        }
    }

    protected function updateSeq()
    {
        $totalCities = $this->modelToUse::find();

        if ($totalCities) {
            $lastCityId = (int) $totalCities->getLast()->id;

            if ($lastCityId > 100000) {
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
    public function updateCity(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' city';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating city.';
        }
    }

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
    }
}