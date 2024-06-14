<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoTimezones as GeoTimezonesModel;

class GeoTimezones extends BasePackage
{
    protected $modelToUse = GeoTimezonesModel::class;

    protected $packageName = 'geoTimezones';

    public $geoTimezones;

    public function addTimezone(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['zone_name'] . ' timezone');
        } else {
            $this->addResponse('Error adding new timezone.', 1);
        }
    }

    public function updateTimezone(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['zone_name'] . ' timezone');
        } else {
            $this->addResponse('Error updating timezone.', 1);
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