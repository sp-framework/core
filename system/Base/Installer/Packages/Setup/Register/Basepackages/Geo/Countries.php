<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Helper\Json;

class Countries
{
    protected $db;

    public function register($db, $localContent)
    {
        $this->db = $db;

        $countries =
            Json::decode(
                $localContent->read(
                    '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/AllCountries.json'
                ),
                true
            );

        $this->registerAllCountries($countries);
    }

    protected function registerAllCountries($countries)
    {
        foreach ($countries as $key => $country) {
            $this->db->insertAsDict(
                'basepackages_geo_countries',
                [
                    'id'                => $country['id'],
                    'name'              => $country['name'],
                    'iso3'              => $country['iso3'],
                    'iso2'              => $country['iso2'],
                    'phone_code'        => $country['phone_code'],
                    'capital'           => $country['capital'],
                    'currency'          => $country['currency'],
                    'currency_symbol'   => $country['currency_symbol'],
                    'currency_enabled'  => 0,
                    'native'            => $country['native'],
                    'region'            => $country['region'],
                    'subregion'         => $country['subregion'],
                    'emoji'             => $country['emoji'],
                    'emojiU'            => $country['emojiU'],
                    'translations'      => Json::encode($country['translations']),
                    'latitude'          => $country['latitude'],
                    'longitude'         => $country['longitude'],
                    'installed'         => 0,
                    'enabled'           => 0,
                ]
            );
        }
    }
}