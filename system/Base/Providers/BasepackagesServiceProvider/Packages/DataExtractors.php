<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use System\Base\BasePackage;

class DataExtractors extends BasePackage
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/';

    protected $sourceFile;

    protected $trackCounter = 0;

    public $method;

    protected $zip;

    protected $gmtOffsets = [];

    protected $geoCountriesStore;

    protected $geoRegionsStore;

    protected $geoStatesStore;

    protected $geoCitiesStore;

    protected $geoPostcodesStore;

    public function init()
    {
        $this->basepackages->progress->init(null, 'dataextractors');

        $this->zip = new \ZipArchive;

        // /etc/apache2.conf - Change the timeout to 3600 else you will get Gateway Timeout, revert back when done to 300 (5 mins)
        // Timeout 3600

        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 3600) {
            set_time_limit(3600);
        }

        //Increase memory_limit to 2G as the process takes a bit of memory to process the array.
        if ((int) ini_get('memory_limit') < 2048) {
            ini_set('memory_limit', '2048M');
        }

        return $this;
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $this->basepackages->progress->updateProgress($method, null, false);

            $call = call_user_func_array([$this, $method], $arguments);

            $callResult = $call;

            if ($call !== false) {
                $call = true;
            }

            $this->basepackages->progress->updateProgress($method, $call, false);

            return $callResult;
        }
    }

    protected function downloadGeoCountriesData()
    {
        $this->method = 'downloadGeoCountriesData';

        if (!is_dir(base_path($this->sourceDir . 'Geo'))) {
            if (!mkdir(base_path($this->sourceDir . 'Geo'), 0777, true)) {
                $this->addResponse('Unable to create Geo directory', 1);

                return false;
            }
        }

        if (!$download = $this->remoteWebDownload->downloadData(
            'https://github.com/dr5hn/countries-states-cities-database/releases/latest/download/json-countries+states+cities.json.gz',
            base_path($this->sourceDir . 'Geo/json-countries+states+cities.json.gz'),
            $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function downloadGeoPostcodeData()
    {
        $this->method = 'downloadGeoPostcodeData';

        if (!$download = $this->remoteWebDownload->downloadData(
            'https://github.com/dr5hn/countries-states-cities-database/releases/latest/download/json-postcodes.json.gz',
            base_path($this->sourceDir . 'Geo/json-postcodes.json.gz'),
            $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function processDownloadedGeoCountriesData($data)
    {
        $this->method = 'downloadGeoPostcodeData';

        $this->ungzData('Geo/json-countries+states+cities.json.gz');
        $this->ungzData('Geo/json-postcodes.json.gz');

        $countries = [];

        try {
            if ($this->localContent->fileExists($this->sourceDir . 'Geo/json-countries+states+cities.json')) {
                $this->sourceFile = $this->helper->decode($this->localContent->read($this->sourceDir . 'Geo/json-countries+states+cities.json'), true);
            }

            if ($this->localContent->fileExists($this->sourceDir . 'Geo/json-postcodes.json')) {
                $postcodesArr = $this->helper->decode($this->localContent->read($this->sourceDir . 'Geo/json-postcodes.json'), true);
            }
        } catch (FilesystemException | UnableToReadFile | UnableToCheckExistence | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->sourceFile && is_array($this->sourceFile)) {
            foreach ($this->sourceFile as $country) {
                $countryKey = $country['iso2'];

                $states = $country['states'];

                unset($country['states']);

                $countries[$countryKey] = $country;

                if ($states && is_array($states) && count($states) > 0) {
                    foreach ($states as $state) {
                        $cities = $state['cities'];

                        unset($state['cities']);

                        $countries[$countryKey]['states'][$country['id'] . '-' . $state['id']] = $state;

                        foreach ($cities as $city) {
                            $cityName = str_replace(' ', '' , strtolower($city['name']));

                            $countries[$countryKey]['states'][$country['id'] . '-' . $state['id']]['cities'][$cityName] = $city;
                        }
                    }

                    $countries[$countryKey]['states'][$country['id'] . '-' . $state['id']]['postcodes'] = [];
                }
            }
        }

        $postCodes = [];

        if ($postcodesArr && is_array($postcodesArr)) {
            foreach ($postcodesArr as $postcode) {
                if (!isset($postCodes[$postcode['country_id'] . '-' . $postcode['state_id']])) {
                    $postCodes[$postcode['country_id'] . '-' . $postcode['state_id']] = [];
                }

                $postCodes[$postcode['country_id'] . '-' . $postcode['state_id']][$postcode['id']]['id'] = $postcode['id'];
                $postCodes[$postcode['country_id'] . '-' . $postcode['state_id']][$postcode['id']]['code'] = $postcode['code'];
                $postCodes[$postcode['country_id'] . '-' . $postcode['state_id']][$postcode['id']]['name'] = $postcode['locality_name'];
                $postCodes[$postcode['country_id'] . '-' . $postcode['state_id']][$postcode['id']]['city_id'] = $postcode['city_id'];
            }
        }

        foreach ($countries as $countryKey => &$country) {
            if (!in_array($country['iso2'], $data['countries'])) {
                continue;
            }

            if (isset($country['states'])) {
                foreach ($country['states'] as &$state) {
                    if (isset($postCodes[$country['id'] . '-' . $state['id']])) {
                        $countries[$countryKey]['states'][$country['id'] . '-' . $state['id']]['postcodes'] = $postCodes[$country['id'] . '-' . $state['id']];
                    }
                }
            }

            try {
                $this->localContent->write($this->sourceDir . 'Geo/' . $countryKey . '.json', $this->helper->encode($country));
            } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            unset($country['states']);
        }

        try {
            $this->localContent->write($this->sourceDir . 'Geo/AllCountries.json', $this->helper->encode($countries));
        } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    protected function processCountryStatesCititesPostcodesData($data)
    {
        $this->method = 'processCountryStatesCititesPostcodesData';

        $installedCountries = [];

        if ($this->ff) {
            $this->geoCountriesStore = $this->ff->store('basepackages_geo_countries');
            $this->geoRegionsStore = $this->ff->store('basepackages_geo_regions');
            $this->geoStatesStore = $this->ff->store('basepackages_geo_states');
            $this->geoCitiesStore = $this->ff->store('basepackages_geo_cities');
            $this->geoPostcodesStore = $this->ff->store('basepackages_geo_postcodes');
        }

        foreach ($data['countries'] as $countryIso2) {
            try {
                if ($this->localContent->fileExists($this->sourceDir . 'Geo/' . $countryIso2 . '.json')) {
                    $country = $this->helper->decode($this->localContent->read($this->sourceDir . 'Geo/' . $countryIso2 . '.json'), true);
                }
            } catch (FilesystemException | UnableToReadFile | UnableToCheckExistence | \throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            if (isset($country)) {
                $this->registerStates($country['states'], $country['id']);

                if ($this->ff) {
                    $dbCountry = $this->geoCountriesStore->findById((int) $country['id']);
                } else if ($this->db) {
                    $dbCountry = $this->db->fetchAll(
                        "SELECT * FROM basepackages_geo_countries WHERE id LIKE :id",
                        \Phalcon\Db\Enum::FETCH_ASSOC,
                        [
                            "id" => $country['id'],
                        ]
                    );

                    if (isset($dbCountry[0])) {
                        $dbCountry = $dbCountry[0];
                    } else {
                        $dbCountry = false;
                    }
                }

                $dbCountry['installed'] = 1;

                if (isset($data['enabled'])) {
                    $dbCountry['enabled'] = 1;
                }

                array_push($installedCountries, $dbCountry['name']);

                if ($this->ff) {
                    $this->geoCountriesStore->updateOrInsert($dbCountry, false);
                } else if ($this->db) {
                    $this->db->insertAsDict('basepackages_geo_countries', $dbCountry);
                }
            }
        }

        $this->addResponse('Installed countries : ' . implode(',', $installedCountries));

        return true;
    }

    protected function ungzData($fileName)
    {
        try {
            // Name of the output file (remove .gz)
            $outFileName = str_replace('.gz', '', $fileName);

            // Open the gzipped file in read-binary mode
            $gzFile = gzopen(base_path($this->sourceDir . $fileName), 'rb');
            // Open/Create the output file in write-binary mode
            $outFile = fopen(base_path($this->sourceDir . $outFileName), 'wb');

            // Read and write until the end of the compressed file
            while (!gzeof($gzFile)) {
                // Read 4KB at a time
                fwrite($outFile, gzread($gzFile, 4096));
            }

            // Close the file pointers
            fclose($outFile);
            gzclose($gzFile);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    protected function downloadTimezoneData()
    {
        $this->method = 'downloadTimezoneData';

        if (!is_dir(base_path($this->sourceDir . 'Geo'))) {
            if (!mkdir(base_path($this->sourceDir . 'Geo'), 0777, true)) {
                $this->addResponse('Unable to create Geo directory', 1);

                return false;
            }
        }

        if (!$download = $this->remoteWebDownload->downloadData(
            'https://en.wikipedia.org/wiki/List_of_tz_database_time_zones',
            base_path($this->sourceDir . 'Geo/tz.txt'),
            $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function processTimezoneData()
    {
        $wikiTz = [];

        include('DataExtractors/vendor/Simplehtmldom.php');

        try {
            $html = str_get_html($this->localContent->read($this->sourceDir . 'Geo/tz.txt'));
        } catch (FilesystemException | UnableToReadFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $table = $html->find('table.wikitable tbody');

        if ($table[0] && $table[0]->children) {
            foreach ($table[0]->children as $tr) {
                if (isset($tr->children[3]) && strtolower(trim($tr->children[3]->plaintext)) === 'canonical') {
                    $zoneName = trim($tr->children[1]->plaintext);

                    $zoneKey = strtolower(str_replace('/', '', $zoneName));

                    $wikiTz[$zoneKey]['zoneName'] = trim($tr->children[1]->plaintext);
                    $wikiTz[$zoneKey]['tzName'] = trim($tr->children[2]->plaintext);
                    if ($wikiTz[$zoneKey]['tzName'] === '') {
                        $wikiTz[$zoneKey]['tzName'] = $wikiTz[$zoneKey]['zoneName'];
                    }
                    //Wikipedia article does not have a minus sign instead it has '−'
                    $gmtOffset = str_replace('−', '-', trim($tr->children[4]->plaintext));
                    $gmtOffsetDST = str_replace('−', '-', trim($tr->children[5]->plaintext));
                    $wikiTz[$zoneKey]['gmtOffsetName'] = 'UTC' . $gmtOffset;
                    $wikiTz[$zoneKey]['gmtOffset'] = $this->getGMTOffset($gmtOffset);
                    $wikiTz[$zoneKey]['abbreviation'] = trim($tr->children[6]->plaintext);
                    $wikiTz[$zoneKey]['gmtOffsetNameDST'] = 'UTC' . $gmtOffsetDST;
                    $wikiTz[$zoneKey]['gmtOffsetDST'] = $this->getGMTOffset($gmtOffsetDST);
                    if (count($tr->children) === 10) {
                        $wikiTz[$zoneKey]['abbreviationDST'] = trim($tr->children[7]->plaintext);
                    } else {
                        $wikiTz[$zoneKey]['abbreviationDST'] = '-';
                    }
                }
            }
        }

        if (count($wikiTz) === 0) {
            $this->addResponse('Not able to extract tz data.', 1);

            return false;
        }

        try {
            $this->localContent->write($this->sourceDir . 'Geo/TimeZones.json', $this->helper->encode($wikiTz));
        } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $this->addResponse('Downloaded and extract Tz data');

        return true;
    }

    protected function getGMTOffset($gmtOffset)
    {
        if (str_contains($gmtOffset, '00:00')) {
            return 0;
        }

        if (isset($this->gmtOffsets[$gmtOffset])) {
            return $this->gmtOffsets[$gmtOffset];
        }

        $this->gmtOffsets[$gmtOffset] = Carbon::now($gmtOffset)->utcOffset();

        return $this->gmtOffsets[$gmtOffset];
    }

    protected function processGeoCountriesData()
    {
        $this->method = 'processGeoCountriesData';

        if ($this->ff) {
            $this->geoCountriesStore = $this->ff->store('basepackages_geo_countries');
            $this->geoRegionsStore = $this->ff->store('basepackages_geo_regions');
        }

        try {
            if ($this->localContent->fileExists($this->sourceDir . 'Geo/AllCountries.json')) {
                $countries = $this->helper->decode($this->localContent->read($this->sourceDir . 'Geo/AllCountries.json'), true);
            }
        } catch (FilesystemException | UnableToReadFile | UnableToCheckExistence | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if (!isset($countries)) {
            $this->addResponse('All Countries database does not exist', 1);

            return false;
        }

        foreach ($countries as $key => $country) {
            if ($this->ff) {
                $dbCountry = $this->geoCountriesStore->findById((int) $country['id']);
            } else if ($this->db) {
                $dbCountry = $this->db->fetchAll(
                    "SELECT * FROM basepackages_geo_countries WHERE id LIKE :id",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        "id" => $country['id'],
                    ]
                );

                if (isset($dbCountry[0])) {
                    $dbCountry = $dbCountry[0];
                } else {
                    $dbCountry = false;
                }
            }

            if (!$dbCountry) {
                $dbCountry =
                    [
                        'id'                => $country['id'],
                        'name'              => $country['name'],
                        'native'            => $country['native'],
                        'nationality'       => $country['nationality'],
                        'capital'           => $country['capital'],
                        'iso2'              => $country['iso2'],
                        'iso3'              => $country['iso3'],
                        'currency'          => $country['currency'],
                        'currency_name'     => $country['currency_name'],
                        'currency_symbol'   => $country['currency_symbol'],
                        'currency_enabled'  => 0,
                        'region_id'         => $country['region_id'],
                        'region'            => $country['region'],
                        'subregion_id'      => $country['subregion_id'],
                        'subregion'         => $country['subregion'],
                        'numeric_code'      => $country['numeric_code'],
                        'phone_code'        => $country['phonecode'],
                        'tld'               => $country['tld'],
                        'emoji'             => $country['emoji'],
                        'emojiU'            => $country['emojiU'],
                        'latitude'          => (int) $country['latitude'],
                        'longitude'         => (int) $country['longitude'],
                        'translations'      => $this->helper->encode($country['translations']),
                        'installed'         => 0,
                        'enabled'           => 0
                    ];
            } else {
                $installed = $dbCountry['installed'];
                $enabled = $dbCountry['enabled'];
                $currency_enabled = $dbCountry['currency_enabled'];

                $dbCountry = array_merge($dbCountry, $country);

                $dbCountry['installed'] = $installed;
                $dbCountry['enabled'] = $enabled;
                $dbCountry['currency_enabled'] = $currency_enabled;
            }

            if ($this->ff) {
                $this->geoCountriesStore->updateOrInsert($dbCountry, false);
            } else if ($this->db) {
                $this->db->insertAsDict('basepackages_geo_countries', $dbCountry);
            }

            if (strlen($dbCountry['region']) > 0 &&
                strlen($dbCountry['subregion']) > 0
            ) {
                $this->checkRegion($dbCountry);
            }
        }

        if ($this->ff) {
            $this->geoCountriesStore->reIndexStore()->count(true);
            $this->geoRegionsStore->reIndexStore()->count(true);
        }

        return true;
    }

    protected function checkRegion($dbCountry)
    {
        $subregion = false;

        if ($this->ff) {
            $subregion = $this->geoRegionsStore->findById($dbCountry['subregion_id']);
        } else if ($this->db) {
            $subregion =
                $this->db->fetchAll(
                    "SELECT * FROM basepackages_geo_regions WHERE id LIKE :id",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        "id" => $dbCountry['subregion_id'],
                    ]
                );

            if (isset($subregion[0])) {
                $subregion = $subregion[0];
            } else {
                $subregion = false;
            }
        }

        if (!$subregion) {
            $subregion['id'] = $dbCountry['subregion_id'];
            $subregion['name'] = $dbCountry['subregion'];
            $subregion['parent_region_id'] = $dbCountry['region_id'];
        } else {
            $subregion['name'] = $dbCountry['subregion'];
        }

        if ($this->ff) {
            $this->geoRegionsStore->updateOrInsert($subregion, false);
        } else if ($this->db) {
            $this->db->insertAsDict('basepackages_geo_regions', $subregion);
        }


        $region = false;

        if ($this->ff) {
            $region = $this->geoRegionsStore->findById($dbCountry['region_id']);
        } else if ($this->db) {
            $region =
                $this->db->fetchAll(
                    "SELECT * FROM basepackages_geo_regions WHERE id LIKE :id",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        "id" => $dbCountry['region_id'],
                    ]
                );

            if (isset($region[0])) {
                $region = $region[0];
            } else {
                $region = false;
            }
        }

        if (!$region) {
            $region['id'] = $dbCountry['region_id'];
            $region['name'] = $dbCountry['region'];
            $region['parent_region_id'] = null;
        } else {
            $region['name'] = $dbCountry['region'];
        }

        if ($this->ff) {
            $this->geoRegionsStore->updateOrInsert($region, false);
        } else if ($this->db) {
            $this->db->insertAsDict('basepackages_geo_regions', $region);
        }
    }

    protected function registerStates($statesData, $country_id)
    {
        $counter = 1;
        foreach ($statesData as $key => $state) {
            $this->basepackages->progress->updateProgress(
                method: $this->method,
                counters: ['stepsTotal' => count($statesData), 'stepsCurrent' => $counter],
                text: 'Registering cities & postcodes for state ' . $state['name'] . '...'
            );

            $state['country_id'] = $country_id;

            if (isset($state['cities'])) {
                $cities = $state['cities'];
                unset($state['cities']);
            }

            if (isset($state['postcodes'])) {
                $postcodes = $state['postcodes'];
                unset($state['postcodes']);
            }

            $dbState = false;

            if ($this->ff) {
                $dbState = $this->geoStatesStore->findById($state['id']);
            } else if ($this->db) {
                $dbState =
                    $this->db->fetchAll(
                        "SELECT * FROM basepackages_geo_states WHERE id LIKE :id",
                        \Phalcon\Db\Enum::FETCH_ASSOC,
                        [
                            "id" => $state['id'],
                        ]
                    );

                if (isset($dbState[0])) {
                    $dbState = $dbState[0];
                } else {
                    $dbState = false;
                }
            }

            if (!$dbState) {
                $dbState = $state;
            } else {
                $dbState = array_merge($dbState, $state);
            }

            if ($this->ff) {
                $this->geoStatesStore->updateOrInsert($dbState, false);
            } else if ($this->db) {
                $this->db->insertAsDict('basepackages_geo_states', $dbState);
            }

            if (isset($cities)) {
                $this->registerCities($cities, $country_id, $state['id']);
            }

            if (isset($postcodes)) {
                $this->registerPostcodes($postcodes, $country_id, $state['id']);
            }

            $counter++;
        }

        $this->geoStatesStore->count(true);
        $this->geoCitiesStore->count(true);
        $this->geoPostcodesStore->count(true);
    }

    protected function registerCities($citiesData, $country_id, $state_id)
    {
        foreach ($citiesData as $key => $city) {
            $city['state_id'] = $state_id;
            $city['country_id'] = $country_id;

            $dbCity = false;

            if ($this->ff) {
                $dbCity = $this->geoCitiesStore->findById($city['id']);
            } else if ($this->db) {
                $dbCity =
                    $this->db->fetchAll(
                        "SELECT * FROM basepackages_geo_cities WHERE id LIKE :id",
                        \Phalcon\Db\Enum::FETCH_ASSOC,
                        [
                            "id" => $city['id'],
                        ]
                    );

                if (isset($dbCity[0])) {
                    $dbCity = $dbCity[0];
                } else {
                    $dbCity = false;
                }
            }

            if (!$dbCity) {
                $dbCity = $city;
            } else {
                $dbCity = array_merge($dbCity, $city);
            }

            if ($this->ff) {
                $this->geoCitiesStore->updateOrInsert($dbCity, false);
            } else if ($this->db) {
                $this->db->insertAsDict('basepackages_geo_cities', $dbCity);
            }
        }
    }

    protected function registerPostcodes($postcodesData, $country_id, $state_id)
    {
        foreach ($postcodesData as $key => $postcode) {
            $postcode['state_id'] = $state_id;
            $postcode['country_id'] = $country_id;

            $dbPostcode = false;

            if ($this->ff) {
                $dbPostcode = $this->geoPostcodesStore->findById($postcode['id']);
            } else if ($this->db) {
                $dbPostcode =
                    $this->db->fetchAll(
                        "SELECT * FROM basepackages_geo_postcodes WHERE id LIKE :id",
                        \Phalcon\Db\Enum::FETCH_ASSOC,
                        [
                            "id" => $postcode['id'],
                        ]
                    );

                if (isset($dbPostcode[0])) {
                    $dbPostcode = $dbPostcode[0];
                } else {
                    $dbPostcode = false;
                }
            }

            if (!$dbPostcode) {
                $dbPostcode = $postcode;
            } else {
                $dbPostcode = array_merge($dbPostcode, $postcode);
            }

            if ($this->ff) {
                $this->geoPostcodesStore->updateOrInsert($dbPostcode, false);
            } else if ($this->db) {
                $this->db->insertAsDict('basepackages_geo_postcodes', $dbPostcode);
            }
        }
    }

    //Dictionary
    //Download Dictionary data from https://raw.githubusercontent.com/dwyl/english-words/master/words_alpha.txt and store in data folder
    protected function downloadDictionaryData()
    {
        $this->method = 'downloadDictionaryData';

        if (!is_dir(base_path($this->sourceDir . 'Dictionary'))) {
            if (!mkdir(base_path($this->sourceDir . 'Dictionary'), 0777, true)) {
                $this->addResponse('Unable to create Dictionary directory', 1);

                return false;
            }
        }

        if (!$download = $this->remoteWebDownload->downloadData(
            'https://raw.githubusercontent.com/dwyl/english-words/master/words_alpha.txt',
            base_path($this->sourceDir . 'Dictionary/words_alpha.txt'),
            $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function processDictionaryData()
    {
        $this->method = 'processDictionaryData';

        try {
            $words = $this->localContent->readStream($this->sourceDir . 'Dictionary/words_alpha.txt');
        } catch (FilesystemException | UnableToReadFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $alphas = range('a','z');

        $wordsArr = [];

        while(!feof($words)) {
            $word = stream_get_line($words, 0, "\n");

            foreach ($alphas as $alpha) {
                if (str_starts_with($word, $alpha)) {
                    $stringLength = strlen($word) - 1;

                    if (!isset($wordsArr[$stringLength][$alpha])) {
                        $wordsArr[$stringLength][$alpha] = [];
                    }
                    array_push($wordsArr[$stringLength][$alpha], trim($word));
                }
            }
        }

        foreach ($wordsArr as $length => $chars) {
            foreach ($chars as $charKey => $charValue) {
                try {
                    $this->localContent->write($this->sourceDir . 'Dictionary/' . $length . '/' . $charKey . '.json', $this->helper->encode($chars[$charKey]));
                } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
                    $this->addResponse($e->getMessage(), 1);

                    return false;
                }
            }
        }

        fclose($words);

        $this->addResponse('Downloaded and extracted dictionary data');

        return true;
    }

    // ip2location
    protected function downloadIp2locationBinFile($data)
    {
        $this->method = 'downloadIp2locationBinFile';

        if (!is_dir(base_path($this->sourceDir . 'Ip2location'))) {
            if (!mkdir(base_path($this->sourceDir . 'Ip2location'), 0777, true)) {
                $this->addResponse('Unable to create Ip2location directory', 1);

                return false;
            }
        }

        if (!$download = $this->remoteWebDownload->downloadData(
                'https://www.ip2location.com/download/?token=' . $data['token'] . '&file=' . $data['bin_file_code'],
                base_path($this->sourceDir . 'Ip2location/' . $data['bin_file_code'] . '.ZIP'),
                $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function downloadIp2locationProxyFile($data)
    {
        $this->method = 'downloadIp2locationProxyFile';

        if (!is_dir(base_path($this->sourceDir . 'Ip2location'))) {
            if (!mkdir(base_path($this->sourceDir . 'Ip2location'), 0777, true)) {
                $this->addResponse('Unable to create Ip2location directory', 1);

                return false;
            }
        }

        if (!$download = $this->remoteWebDownload->downloadData(
                'https://www.ip2location.com/download/?token=' . $data['token'] . '&file=' . $data['proxy_file_code'],
                base_path($this->sourceDir . 'Ip2location/' . $data['proxy_file_code'] . '.ZIP'),
                $this->method)
        ) {
            $this->addResponse('Download resulted in : ' . $this->remoteWebDownload->getDownload()->getStatusCode(), 1);

            return false;
        }

        return true;
    }

    protected function unzipIp2locationBinFile($data)
    {
        $this->method = 'unzipIp2locationBinFile';

        if ($this->zip->open(base_path($this->sourceDir . 'Ip2location/' . $data['bin_file_code'] . '.ZIP')) === true) {
            $this->zip->extractTo(base_path($this->sourceDir . 'Ip2location/'));

            $this->zip->close();

            return true;
        }

        $this->addResponse('Unable to unzip BIN file', 1);

        return false;
    }

    protected function unzipIp2locationProxyFile($data)
    {
        $this->method = 'unzipIp2locationProxyFile';

        if ($this->zip->open(base_path($this->sourceDir . 'Ip2location/' . $data['proxy_file_code'] . '.ZIP')) === true) {
            $this->zip->extractTo(base_path($this->sourceDir . 'Ip2location/'));

            $this->zip->close();

            return true;
        }

        $this->addResponse('Unable to unzip Proxy file', 1);

        return false;
    }

    protected function moveIp2locationBinFile($data)
    {
        $this->method = 'moveIp2locationBinFile';

        $folderContents = $this->basepackages->utils->scanDir($this->sourceDir . 'Ip2location/');

        if (count($folderContents['files']) === 0) {
            $this->addResponse('Data was not extracted correctly, re-run download', 1);

            return false;
        }

        //Rename file to the bin file code name.
        try {
            $renamedFile = false;

            foreach ($folderContents['files'] as $file) {
                if (str_contains($file, '.BIN') && str_contains($file, 'DB3')) {
                    $binFile = $data['bin_file_code'] . '.BIN';
                } else {
                    continue;
                }

                try {
                    $this->localContent->move($file, $this->sourceDir . 'Ip2location/' . $binFile);

                    $this->localContent->delete($file);

                    $renamedFile = true;
                } catch (\throwable | FilesystemException | UnableToMoveFile | UnableToDeleteFile $e) {
                    $this->addResponse($e->getMessage(), 1);

                    return false;
                }

                break;
            }

            if (!$renamedFile) {
                throw new \Exception('Data was not extracted correctly, re-run download');
            }
        } catch (\throwable | UnableToMoveFile | FilesystemException $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    protected function moveIp2locationProxyFile($data)
    {
        $this->method = 'moveIp2locationProxyFile';

        $folderContents = $this->basepackages->utils->scanDir($this->sourceDir . 'Ip2location/');

        if (count($folderContents['files']) === 0) {
            $this->addResponse('Data was not extracted correctly, re-run download', 1);

            return false;
        }

        //Rename file to the bin file code name.
        try {
            $renamedFile = false;

            foreach ($folderContents['files'] as $file) {
                if (str_contains($file, '.BIN') && str_contains($file, 'PX3')) {
                    $proxyFile = $data['proxy_file_code'] . '.BIN';
                } else {
                    continue;
                }

                try {
                    $this->localContent->move($file, $this->sourceDir . 'Ip2location/' . $proxyFile);

                    $this->localContent->delete($file);

                    $renamedFile = true;
                } catch (\throwable | FilesystemException | UnableToMoveFile | UnableToDeleteFile $e) {
                    $this->addResponse($e->getMessage(), 1);

                    return false;
                }

                break;
            }

            if (!$renamedFile) {
                throw new \Exception('Data was not extracted correctly, re-run download');
            }
        } catch (\throwable | UnableToMoveFile | FilesystemException $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    protected function updateIp2locationInfo($data)
    {
        $this->method = 'updateIp2locationInfo';

        $ip2locationinfo = [];
        $ip2locationinfo['downloaded_on'] = (\Carbon\Carbon::now())->toDateTimeString();

        if (isset($data['bin']) && $data['bin'] == 'true') {
            try {
                $ip2locationinfo['bin_file_name'] = $data['bin_file_code'] . '.BIN';
                $ip2locationBin =
                    new \IP2Location\Database(
                        base_path($this->sourceDir . 'Ip2location/' . $data['bin_file_code'] . '.BIN'),
                        constant('\IP2Location\Database::FILE_IO')
                    );

                $ip2locationinfo['bin_file_version'] = $ip2locationBin->getDatabaseVersion();
            } catch (\throwable $e) {
                $detector = new FinfoMimeTypeDetector();
                $mimeType = $detector->detectMimeTypeFromFile(base_path($this->sourceDir . 'Ip2location/' . $data['bin_file_code'] . '.ZIP'));

                if ($mimeType === 'text/plain') {
                    $this->addResponse(strtolower($this->localContent->read($this->sourceDir . 'Ip2location/' . $data['bin_file_code'] . '.ZIP')), 1);
                } else {
                    $this->addResponse('Unable to read BIN file, re-run download', 1);
                }

                return false;
            }
        }
        if (isset($data['proxy']) && $data['proxy'] == 'true') {
            try {
                $ip2locationinfo['proxy_file_name'] = $data['proxy_file_code'] . '.BIN';
                $ip2locationProxy =
                    new \IP2Location\Database(
                        base_path($this->sourceDir . 'Ip2location/' . $data['proxy_file_code'] . '.BIN'),
                        constant('\IP2Location\Database::FILE_IO')
                    );

                $ip2locationinfo['proxy_file_version'] = $ip2locationProxy->getDatabaseVersion();
            } catch (\throwable $e) {
                $detector = new FinfoMimeTypeDetector();
                $mimeType = $detector->detectMimeTypeFromFile(base_path($this->sourceDir . 'Ip2location/' . $data['proxy_file_code'] . '.ZIP'));

                if ($mimeType === 'text/plain') {
                    $this->addResponse(strtolower($this->localContent->read($this->sourceDir . 'Ip2location/' . $data['proxy_file_code'] . '.ZIP')), 1);
                } else {
                    $this->addResponse('Unable to read BIN file, re-run download', 1);
                }

                return false;
            }
        }

        try {
            $this->localContent->write($this->sourceDir . 'Ip2location/info.json', $this->helper->encode($ip2locationinfo));
        } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $this->addResponse('Downloaded & extracted latest ip2location database', 0, ['ip2locationInfo' => $this->getIp2locationInfo()]);

        return true;
    }

    public function getIp2locationInfo()
    {
        try {
            if ($this->localContent->fileExists($this->sourceDir . 'Ip2location/info.json')) {
                return $this->helper->decode($this->localContent->read($this->sourceDir . 'Ip2location/info.json'), true);
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);
        }

        return false;
    }
}