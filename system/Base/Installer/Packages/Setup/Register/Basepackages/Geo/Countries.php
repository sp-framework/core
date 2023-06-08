<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Helper\Json;

class Countries
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    public function register($db, $ff, $localContent)
    {
        $countries =
            Json::decode(
                $localContent->read(
                    '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/AllCountries.json'
                ),
                true
            );

        foreach ($countries as $key => $country) {
            $countryToInsert =
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
                    'latitude'          => (int) $country['latitude'],
                    'longitude'         => (int) $country['longitude'],
                    'installed'         => 0,
                    'enabled'           => 0,
                ];

            if ($db) {
                $db->insertAsDict('basepackages_geo_countries', $countryToInsert);
            }

            if ($ff) {
                $countryStore = $ff->store('basepackages_geo_countries');

                $countryStore->updateOrInsert($countryToInsert);
            }
        }
    }

    public function downloadSelectedCountryStatesAndCities($ff, $localContent, $remoteWebContent, $country)
    {
        $countryStore = $ff->store('basepackages_geo_countries');

        $country = $countryStore->findOneBy(['iso3', '=', $country]);

        $zip = new \ZipArchive;

        try {
            if ($localContent->fileExists($this->sourceDir . $country['iso2'] . '.json')) {
                return true;
            }

            $downloadCountry =
                $remoteWebContent
                    ->request(
                        'GET',
                        'https://dev.bazaari.com.au/sp-public/geodata/raw/branch/master/' . $country['iso2'] . '.zip',
                        ['verify' => false, 'connect_timeout' => 5]
                    )->getBody()->getContents();

            $localContent->write($this->sourceDir . $country['iso2'] . '.zip', $downloadCountry);

            if ($zip->open(base_path($this->sourceDir . $country['iso2'] . '.zip'))) {
                if (!$zip->extractTo('/')) {
                    return false;
                }
            } else {
                return false;
            }

            $zip->close(base_path($this->sourceDir . $country['iso2'] . '.zip'));

            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    public function registerSelectedCountryStatesAndCities($ff, $localContent, $remoteWebContent, $country, $ip2location)
    {
        $countriesStore = $ff->store('basepackages_geo_countries');
        $country = $countriesStore->findOneBy(['iso3', '=', $country]);
        $statesStore = $ff->store('basepackages_geo_states');
        $citiesStore = $ff->store('basepackages_geo_cities');
        $ipv4Store = $ff->store('basepackages_geo_cities_ip2locationv4');
        $ipv6Store = $ff->store('basepackages_geo_cities_ip2locationv6');

        try {
            set_time_limit(300);//5Mins

            if ($ip2location) {
                set_time_limit(900);//15Mins
            }

            $countryData = Json::decode($localContent->read($this->sourceDir . $country['iso2'] . '.json'), true);

            foreach ($countryData['states'] as $key => $state) {
                $state['country_id'] = $country['id'];

                if (isset($state['cities'])) {
                    $cities = $state['cities'];
                    unset($state['cities']);
                }

                $statesStore->updateOrInsert($state, false);

                if (isset($cities)) {
                    foreach ($cities as $key => $city) {
                        if (!isset($city['id'])) {
                            continue;
                        }

                        $city['state_id'] = $state['id'];
                        $city['country_id'] = $country['id'];

                        if ($ip2location == 'true' && isset($city['ip2locationv4'])) {
                            $ip2locationv4['id'] = $city['id'];
                            $ip2locationv4['city_id'] = $city['id'];
                            $ip2locationv4['ip2locationv4'] = $city['ip2locationv4'];
                            $ipv4Store->updateOrInsert($ip2locationv4, false);
                        }

                        if ($ip2location == 'true' && isset($city['ip2locationv6'])) {
                            $ip2locationv6['id'] = $city['id'];
                            $ip2locationv6['city_id'] = $city['id'];
                            $ip2locationv6['ip2locationv6'] = $city['ip2locationv6'];
                            $ipv6Store->updateOrInsert($ip2locationv6, false);
                        }

                        unset($city['ip2locationv4']);
                        unset($city['ip2locationv6']);

                        $citiesStore->updateOrInsert($city, false);
                    }
                }
            }
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }
}