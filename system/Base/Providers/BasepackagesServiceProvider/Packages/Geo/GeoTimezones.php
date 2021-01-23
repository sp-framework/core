<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\GeoTimezones as GeoTimezonesModel;

class GeoTimezones extends BasePackage
{
    protected $modelToUse = GeoTimezonesModel::class;

    protected $packageName = 'geotimezones';

    public $geotimezones;

    public function addTimezone(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['zone_name'] . ' timezone';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new timezone.';
        }
    }

    public function updateTimezone(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['zone_name'] . ' timezone';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating timezone.';
        }
    }

    public function searchTimezone(string $timezoneQueryString)
    {
        $searchTimezones =
            $this->getByParams(
                [
                    'conditions'    => 'tz_name LIKE :tName:',
                    'bind'          => [
                        'tName'     => '%' . $timezoneQueryString . '%'
                    ]
                ]
            );

        if ($searchTimezones) {
            $countries = [];

            foreach ($searchTimezones as $timezoneKey => $timezoneValue) {
                $countries[$timezoneKey] = $timezoneValue;
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->countries = $countries;

            return true;
        }
    }
}