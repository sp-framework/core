<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;

use System\Base\BasePackage;

class DataExtractors extends BasePackage
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/';

    protected $sourceFile;

    protected $trackCounter = 0;

    public $method;

    protected $zip;

    protected $gmtOffsets = [];

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

        return $this->downloadData(
            'https://github.com/dr5hn/countries-states-cities-database/releases/latest/download/json-countries+states+cities.json.gz',
            base_path($this->sourceDir . 'Geo/json-countries+states+cities.json.gz')
        );
    }

    protected function downloadGeoPostcodeData()
    {
        $this->method = 'downloadGeoPostcodeData';

        return $this->downloadData(
            'https://github.com/dr5hn/countries-states-cities-database/releases/latest/download/json-postcodes.json.gz',
            base_path($this->sourceDir . 'Geo/json-postcodes.json.gz')
        );
    }

    protected function processGeoCountriesData()
    {
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
        } catch (FilesystemException | UnableToReadFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->sourceFile && is_array($this->sourceFile)) {
            foreach ($this->sourceFile as $country) {
                if (!in_array($country['iso2'], $this->postData()['countries'])) {
                    continue;
                }

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
            if (isset($country['states'])) {
                foreach ($country['states'] as &$state) {
                    if (isset($postCodes[$country['id'] . '-' . $state['id']])) {
                        $countries[$countryKey]['states'][$country['id'] . '-' . $state['id']]['postcodes'] = $postCodes[$country['id'] . '-' . $state['id']];
                    }
                }
            }

            $this->localContent->write($this->sourceDir . 'Geo/' . $countryKey . '.json', $this->helper->encode($country));

            unset($country['states']);
        }

        $this->localContent->write($this->sourceDir . 'Geo/AllCountries.json', $this->helper->encode($countries));

        return true;
    }

    protected function processGeoDataToDb()
    {
        foreach ($this->postData()['countries'] as $countryIso2) {
            try {
                if ($this->localContent->fileExists($this->sourceDir . 'Geo/' . $countryIso2 . '.json')) {
                    $countryFile = $this->helper->decode($this->localContent->read($this->sourceDir . 'Geo/' . $countryIso2 . '.json'), true);
                }
            } catch (FilesystemException | UnableToReadFile | \throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }
        }

        if (isset($countryFile)) {
            //

            return true;
        }

        $this->addResponse('Not able to read country file', 1);

        return false;
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

        return $this->downloadData(
            'https://en.wikipedia.org/wiki/List_of_tz_database_time_zones',
            base_path('apps/Core/Packages/Devtools/GeoExtractData/Data/tz.txt')
        );
    }

    protected function processTimezoneData()
    {
        $wikiTz = [];

        include('vendor/Simplehtmldom.php');

        $html = str_get_html($this->localContent->read($this->sourceDir . 'tz.txt'));

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
            $this->localContent->write($this->sourceDir . 'TimeZones.json', $this->helper->encode($wikiTz));
        } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
            throw $e;
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

    protected function downloadData($url, $sink)
    {
        $download = $this->remoteWebContent->request(
            'GET',
            $url,
            [
                'progress' => function(
                    $downloadTotal,
                    $downloadedBytes,
                    $uploadTotal,
                    $uploadedBytes
                ) {
                    $counters =
                            [
                                'downloadTotal'     => $downloadTotal,
                                'downloadedBytes'   => $downloadedBytes,
                                'uploadTotal'       => $uploadTotal,
                                'uploadedBytes'     => $uploadedBytes
                            ];

                    if ($downloadedBytes === 0) {
                        return;
                    }

                    //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                    //So this way, we only update progress when there is actually an update.
                    if ($downloadedBytes === $this->trackCounter) {
                        return;
                    }

                    $this->trackCounter = $downloadedBytes;

                    if ($downloadedBytes === $downloadTotal) {
                        $this->basepackages->progress->updateProgress($this->method, true, false, null, $counters);
                    } else {
                        $this->basepackages->progress->updateProgress($this->method, null, false, null, $counters);
                    }
                },
                'verify'            => false,
                'connect_timeout'   => 5,
                'sink'              => $sink,
                'headers'           => [
                    'User-Agent'    => 'Mozilla/5.0 (X11; Linux i686; rv:150.0) Gecko/20100101 Firefox/150.0'
                ]
            ]
        );

        $this->trackCounter = 0;

        if ($download->getStatusCode() === 200) {
            return true;
        }

        $this->addResponse('Download resulted in : ' . $download->getStatusCode(), 1);

        return false;
    }
    //Move this to tools/DataExtractor
    // public function registerSelectedCountryStatesAndCities($ff, $localContent, $country, $ip2location = null, $helper)
    //     $countriesStore = $ff->store('basepackages_geo_countries');
    //     $country = $countriesStore->findOneBy(['iso3', '=', $country]);

    //     $statesStore = $ff->store('basepackages_geo_states');
    //     $citiesStore = $ff->store('basepackages_geo_cities');
    //     $postcodesStore = $ff->store('basepackages_geo_postcodes');

    //     try {
    //         $countryData = $helper->decode($localContent->read($this->sourceDir . $country['iso2'] . '.json'), true);

    //         foreach ($countryData['states'] as $key => $state) {
    //             $state['country_id'] = $country['id'];

    //             if (isset($state['cities'])) {
    //                 $cities = $state['cities'];
    //                 unset($state['cities']);
    //             }

    //             if (isset($state['postcodes'])) {
    //                 $postcodes = $state['postcodes'];
    //                 unset($state['postcodes']);
    //             }

    //             $statesStore->updateOrInsert($state, false);

    //             if (isset($cities)) {
    //                 foreach ($cities as $key => $city) {
    //                     if (!isset($city['id'])) {
    //                         continue;
    //                     }

    //                     $city['state_id'] = $state['id'];
    //                     $city['country_id'] = $country['id'];

    //                     $citiesStore->updateOrInsert($city, false);
    //                 }
    //             }

    //             if (isset($postcodes)) {
    //                 foreach ($postcodes as $key => $postcode) {
    //                     if (!isset($postcode['id'])) {
    //                         continue;
    //                     }

    //                     $postcode['state_id'] = $state['id'];
    //                     $postcode['country_id'] = $country['id'];

    //                     $postcodesStore->updateOrInsert($postcode, false);
    //                 }
    //             }
    //         }

    //         $localContent->delete($this->sourceDir . $country['iso2'] . '.json');
    //         $localContent->delete($this->sourceDir . $country['iso2'] . '.zip');
    //         $countriesStore->count(true);
    //         $statesStore->count(true);
    //         $citiesStore->count(true);
    //         $postcodesStore->count(true);

    //         $country['installed'] = 1;
    //         $country['enabled'] = 1;
    //         $countriesStore->update($country);

    //         return true;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }
}