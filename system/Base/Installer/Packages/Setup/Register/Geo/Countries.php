<?php

namespace System\Base\Installer\Packages\Setup\Register\Geo;

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

        // $countryData =
        //     Json::decode(
        //         $localContent->read(
        //             '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/' . $country . '.json'
        //         ),
        //         true
        //     );

        // $this->registerStates($countryData[$country]['states'], $countryData[$country]['id']);
    }

    protected function registerAllCountries($countries)
    {
        foreach ($countries as $key => $country) {
            $this->db->insertAsDict(
                'geo_countries',
                [
                    'id'                => $country['id'],
                    'name'              => $country['name'],
                    'iso3'              => $country['iso3'],
                    'iso2'              => $country['iso2'],
                    'phone_code'        => $country['phone_code'],
                    'capital'           => $country['capital'],
                    'currency'          => $country['currency'],
                    'currency_symbol'   => $country['currency_symbol'],
                    'native'            => $country['native'],
                    'region'            => $country['region'],
                    'subregion'         => $country['subregion'],
                    'emoji'             => $country['emoji'],
                    'emojiU'            => $country['emojiU'],
                    'installed'         => 0,
                    'enabled'           => 0,
                    'cached'            => 0
                ]
            );

            $this->registerTimezones($country['timezones'], $country['id']);
        }
    }

    protected function registerTimezones($timezonesData, $country_id)
    {
        foreach ($timezonesData as $key => $timezone) {
            $this->db->insertAsDict(
                'geo_timezones',
                [
                    'zone_name'         => $timezone['zoneName'],
                    'tz_name'           => $timezone['tzName'],
                    'gmt_offset'        => $timezone['gmtOffset'],
                    'gmt_offset_name'   => $timezone['gmtOffsetName'],
                    'abbreviation'      => $timezone['abbreviation'],
                    'country_id'        => $country_id
                ]
            );
        }
    }

    // protected function registerStates($statesData, $country_id)
    // {
    //     foreach ($statesData as $key => $state) {
    //         $this->db->insertAsDict(
    //             'geo_states',
    //             [
    //                 'id'            => $statesData[$key]['id'],
    //                 'name'          => $statesData[$key]['name'],
    //                 'state_code'    => $statesData[$key]['state_code'],
    //                 'country_id'    => $country_id
    //             ]
    //         );

    //         $this->registerCities($statesData[$key]['cities'], $country_id, $statesData[$key]['id']);
    //     }
    // }

    // protected function registerCities($citiesData, $country_id, $state_id)
    // {
    //     foreach ($citiesData as $key => $state) {
    //         $this->db->insertAsDict(
    //             'geo_cities',
    //             [
    //                 'id'            => $citiesData[$key]['id'],
    //                 'name'          => $citiesData[$key]['name'],
    //                 'latitude'      => $citiesData[$key]['latitude'],
    //                 'longitude'     => $citiesData[$key]['longitude'],
    //                 'state_id'      => $state_id,
    //                 'country_id'    => $country_id
    //             ]
    //         );
    //     }
    // }
}