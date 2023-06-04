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

        // $this->sqlite->createTable('basepackages_geo_countries', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Countries)->columns());
        // $this->sqlite->addIndex('basepackages_geo_countries', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Countries)->indexes()[0]);
        // $this->sqlite->createTable('basepackages_geo_states', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States)->columns());
        // $this->sqlite->addIndex('basepackages_geo_states', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States)->indexes()[0]);
        // $this->sqlite->addIndex('basepackages_geo_states', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States)->indexes()[1]);
        // $this->sqlite->createTable('basepackages_geo_cities', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities)->columns());
        // $this->sqlite->addIndex('basepackages_geo_cities', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities)->indexes()[0]);
        // $this->sqlite->addIndex('basepackages_geo_cities', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities)->indexes()[1]);
        // $this->sqlite->addIndex('basepackages_geo_cities', 'main', (new \System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities)->indexes()[2]);
        // $countriesStore = $this->ff->use('countries');

        // $countries =
        //     Json::decode(
        //         $this->localContent->read(
        //             '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/AllCountries.json'
        //         ),
        //         true
        //     );

        // foreach ($countries as $key => $country) {
        //     $this->sqlite->insertAsDict(
        //         'basepackages_geo_countries',
        //     // $countriesStore->updateOrInsert(
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
        //         ]
        //     );
        // }

        // die();
        $modelToUse = new \System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCountries;

        $modelToUse->setConnectionService('sqlite');
        $geoCountries = $modelToUse::find(['conditions'=>'']);
        dump($geoCountries);die();
        dump($allCountries);
        // $allCountries = $countriesStore->findAll();
        // $country = $countriesStore->findOneBy(['iso3', '=', 'USA']);
        // $countryData = Json::decode($this->localContent->read($this->sourceDir . $country['iso2'] . '.json'), true);

        // $statesStore = $this->ff->use('states');
        // $citiesStore = $this->ff->use('cities', ['search' => ['min_length' => 1]]);

        // $cal = $statesStore->findOneBy(['name', '=', 'California']);

        // $stateWithCities = $statesStore
        //     ->createQueryBuilder()
        //     ->where(['name', 'like', 'california'])
        //     ->join(function($state) use ($citiesStore) {
        //         return $citiesStore->findBy(['state_id', '=', $state['id']]);
        //     }, 'cities')
        //     ->getQuery()
        //     ->fetch();

        // foreach ($countryData['states'] as $key => $state) {
        //     $state['country_id'] = $country['id'];

        //     if (isset($state['cities'])) {
        //         $cities = $state['cities'];
        //         unset($state['cities']);
        //     }

        //     $statesStore->updateOrInsert($state, false);

        //     if (isset($cities)) {
        //         foreach ($cities as $key => $city) {
        //             $city['state_id'] = $state['id'];
        //             $city['country_id'] = $country['id'];
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

        dump($micro);
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