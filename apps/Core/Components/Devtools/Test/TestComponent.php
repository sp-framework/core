<?php

namespace Apps\Core\Components\Devtools\Test;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    public function viewAction()
    {
        set_time_limit(600);

        $starttime = microtime(true);

        $schema = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            '$id' => 'http://example.com/schema.json',
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 20
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'minLength' => 1,
                    'maxLength' => 50
                ]
            ],
            'required' => ['email']
        ];

        $userStore = $this->ff->store('users', ['search' => ['min_length' => 1], 'indexing' => true, 'minIndexChars' => 2, 'indexes' => ['username'], 'uniqueFields' => ['email']], $schema);

        $userStore->updateOrInsert(
            [
                'username'  => 'sharon3',
                'email'     => 'sharon3@bazaari.com.au',
                'full_name' => [
                    'first_name'    => 'Sharon',
                    'last_name'     => 'Singh'
                ]
            ]
        );
        // $ab = $userStore->findBy([['full_name.last_name', '=', 'singh'],['username', 'LIKE', '%3%']]);
        $ab = $userStore
            ->createQueryBuilder()
            ->where(['full_name.last_name', 'like', 'singh'])
            ->where(['username', 'LIKE', '%ru3'])
            ->getQuery()
            ->fetch();
        // $countriesStore = $this->ff->store('geo_countries', ['search' => ['min_length' => 1], 'indexing' => true, 'minIndexChars' => 2, 'indexes' => ['name']]);
        // $statesStore = $this->ff->store('geo_states',
        //     ['search' => ['min_length' => 1], 'indexing' => true, 'minIndexChars' => 2, 'indexes' => ['name', 'country_id']]
        // );
        // $citiesStore = $this->ff->store('geo_cities',
        //     ['auto_cache' => false, 'search' => ['min_length' => 1], 'indexing' => true, 'minIndexChars' => 2, 'indexes' => ['name', 'state_id', 'country_id']]
        // );
        // $citiesStore->reIndexStore();die();
        // $ipv4Store = $this->ff->store('geo_cities_ip2locationv4');
        // $ipv6Store = $this->ff->store('geo_cities_ip2locationv6');

        // $countries =
        //     Json::decode(
        //         $this->localContent->read(
        //             '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/AllCountries.json'
        //         ),
        //         true
        //     );

        // foreach ($countries as $key => $country) {
        //     $countriesStore->updateOrInsert(
        //         [
        //             'id'                => $country['id'],
        //             'name'              => $country['name'],
        //             'iso3'              => $country['iso3'],
        //             'iso2'              => $country['iso2'],
        //             'phone_code'        => $country['phone_code'],
        //             'capital'           => $country['capital'],
        //             'currency'          => $country['currency'],
        //             'currency_symbol'   => $country['currency_symbol'],
        //             'currency_enabled'  => 0,
        //             'native'            => $country['native'],
        //             'region'            => $country['region'],
        //             'subregion'         => $country['subregion'],
        //             'emoji'             => $country['emoji'],
        //             'emojiU'            => $country['emojiU'],
        //             'translations'      => Json::encode($country['translations']),
        //             'latitude'          => $country['latitude'],
        //             'longitude'         => $country['longitude'],
        //             'installed'         => 0,
        //             'enabled'           => 0,
        //         ],
        //         false
        //     );
        // }

        // $allCountries = $countriesStore->findAll();
        // $country = $countriesStore->findOneBy(['iso3', '=', 'USA']);
        // $countryData = Json::decode($this->localContent->read($this->sourceDir . $country['iso2'] . '.json'), true);

        // $cal = $statesStore->findOneBy(['name', '=', 'California']);
        // $ab = $citiesStore->findBy(['name', '=', 'San Jose']);

        // foreach ($countryData['states'] as $key => $state) {
        //     $state['country_id'] = $country['id'];

        //     if (isset($state['cities'])) {
        //         $cities = $state['cities'];
        //         unset($state['cities']);
        //     }

        //     $statesStore->updateOrInsert($state, false);

        //     if (isset($cities)) {
        //         foreach ($cities as $key => $city) {
        //             if (!isset($city['id'])) {
        //                 continue;
        //             }

        //             $city['state_id'] = $state['id'];
        //             $city['country_id'] = $country['id'];

        //             if (isset($city['ip2locationv4'])) {
        //                 $ip2locationv4['id'] = $city['id'];
        //                 $ip2locationv4['city_id'] = $city['id'];
        //                 $ip2locationv4['ip2locationv4'] = $city['ip2locationv4'];
        //                 $ipv4Store->updateOrInsert($ip2locationv4, false);
        //             }

        //             if (isset($city['ip2locationv6'])) {
        //                 $ip2locationv6['id'] = $city['id'];
        //                 $ip2locationv6['city_id'] = $city['id'];
        //                 $ip2locationv6['ip2locationv6'] = $city['ip2locationv6'];
        //                 $ipv6Store->updateOrInsert($ip2locationv6, false);
        //             }

        //             unset($city['ip2locationv4']);
        //             unset($city['ip2locationv6']);

        //             $citiesStore->updateOrInsert($city, false);
        //         }
        //     }
        // }

        $diff = microtime(true) - $starttime;

        $sec = intval($diff);
        $micro = $diff - $sec;

        // Format the result as you want it
        // $final will contain something like "00:00:02.452"
        // $final = strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro));


        // $stateWithCities =
        //     $statesStore
        //         ->createQueryBuilder()
        //         ->where(['name', 'like', 'california'])
        //         ->join(function($state) use ($citiesStore, $ipv4Store, $ipv6Store) {
        //             $san = $citiesStore
        //                 ->createQueryBuilder()
        //                 ->where(['name', '=', 'San Jose'], ['state_id', '=', $state['id']])
        //                 ->join(function($city) use ($ipv4Store) {
        //                     $ipv4 = $ipv4Store->findById($city['id']);

        //                     if ($ipv4) {
        //                         return $ipv4['ip2locationv4'];
        //                     }
        //                 }, 'ip2locationv4')
        //                 ->join(function($city) use ($ipv6Store) {
        //                     $ipv6 = $ipv6Store->findById($city['id']);

        //                     if ($ipv6) {
        //                         return $ipv6['ip2locationv6'];
        //                     }
        //                 }, 'ip2locationv6');

        //             return $san;
        //         }, 'cities')
        //         ->getQuery()
        //         ->fetch();

        var_dump($micro, $ab);
        // dump($stateWithCities);
        die();
    }

    public function addAction()
    {
        //
    }

    public function updateAction()
    {
        //
    }

    public function removeAction()
    {
        //
    }
}