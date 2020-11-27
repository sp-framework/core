<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\GeoCountries as GeoCountriesModel;
use System\Base\BasePackage;

class GeoCountries extends BasePackage
{
    protected $modelToUse = GeoCountriesModel::class;

    protected $packageName = 'geocountries';

    public $geocountries;

    public function searchCountries(string $countryQueryString)
    {
        $searchCountries =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName:',
                    'bind'          => [
                        'cName'     => '%' . $countryQueryString . '%'
                    ]
                ]
            );

        if ($searchCountries) {
            $countries = [];

            foreach ($searchCountries as $countryKey => $countryValue) {
                $countries[$countryKey] = $countryValue;
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->countries = $countries;

            return true;
        }
    }
}