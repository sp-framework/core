<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class GeoExtractData extends BasePackage
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    protected $sourceFile;

    protected $zip;

    protected $tzLink = "https://en.wikipedia.org/wiki/List_of_tz_database_time_zones";

    public function extractData(string $sourceFile)
    {
        try {
            if ($this->localContent->fileExists($this->sourceDir . $sourceFile . '.json')) {
                $this->sourceFile = Json::decode($this->localContent->read($this->sourceDir . $sourceFile . '.json'), true);

                $this->processJson();
            } else if ($this->localContent->fileExists($this->sourceDir . $sourceFile . '.csv')) {
                $this->sourceFile = Reader::createFromPath(base_path($this->sourceDir . $sourceFile . '.csv'));

                $this->processCSV($sourceFile);
            } else if ($this->localContent->fileExists($this->sourceDir . $sourceFile . '.zip')) {
                if ($this->extractZip($this->sourceDir . $sourceFile . '.zip')) {
                    $this->extractData($sourceFile);
                }
            } else {
                $this->packagesData->responseMessage = 'Source file does not exists!';

                return false;
            }
        } catch (FilesystemException | UnableToReadFile $exception) {
            throw $exception;
        }
    }

    protected function extractZip($sourceFile)
    {
        set_time_limit(120);

        $zip = new \ZipArchive;

        if ($zip->open(base_path($sourceFile))) {
            $zip->extractTo(base_path($this->sourceDir));
        }

        $zip->close($sourceFile);

        return true;
    }

    protected function processCSV($sourceFile)
    {
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

        $countries = [];
        $countryKeys = [];

        $records = Statement::create()->process($this->sourceFile);

        foreach ($records->getRecords() as $key => $record) {
            if ($record[2] === '-' || $record[4] === '-' || $record[5] === '-') {
                continue;
            }

            $stateName = str_replace(' ', '' , strtolower($record[4]));

            $cityName = str_replace(' ', '' , strtolower($record[5]));

            $countries[$record[2]]['states'][$stateName]['name'] = $record[4];

            if (isset($countries[$record[2]]['states'][$stateName]['cities'][$cityName]) &&
                isset($countries[$record[2]]['states'][$stateName]['cities'][$cityName][$sourceFile])
            ) {
                $ipRanges = array_merge($countries[$record[2]]['states'][$stateName]['cities'][$cityName][$sourceFile], [$record[0], $record[1]]);
            } else {
                $ipRanges = [$record[0], $record[1]];
            }

            $countries[$record[2]]['states'][$stateName]['cities'][$cityName] =
                [
                    'name'              => $record[5],
                    "latitude"          => $record[6],
                    "longitude"         => $record[7],
                    "postcode"          => $record[8],
                    "gmtOffset"         => $this->getGMTOffset($record[9]),
                    "gmtOffsetName"     => 'UTC' . $record[9],
                    $sourceFile         => $ipRanges
                ];
        }

        foreach ($countries as $countryKey => $country) {
            $this->localContent->write($this->sourceDir . 'ip2location/' . $countryKey . '.json', JSON::encode($country));

            array_push($countryKeys, $countryKey);
        }

        $this->localContent->write($this->sourceDir . 'ip2location/AllCountries.json', JSON::encode($countryKeys));

        $this->mergeCountryData();
    }

    protected function getGMTOffset($gmtOffset)
    {
        $gmtOffset = str_replace('-', '', str_replace('+', '', $gmtOffset));

        $gmtOffset = explode(':', $gmtOffset);

        if (count($gmtOffset) === 2) {
            $hours = (int) $gmtOffset[0] * 3600;//seconds
            $mins = (int) $gmtOffset[1] * 60;

            $time = $hours + $mins;

            return $time;
        }
    }

    protected function processJson()
    {
        $countries = [];

        if ($this->sourceFile && is_array($this->sourceFile)) {
            foreach ($this->sourceFile as $country) {
                $countryKey = $country['iso2'];

                $states = $country['states'];

                unset($country['states']);

                $countries[$countryKey] = $country;

                if ($states && is_array($states) && count($states) > 0) {
                    foreach ($states as $state) {
                        $stateName = str_replace(' ', '' , strtolower($state['name']));

                        $cities = $state['cities'];

                        unset($state['cities']);

                        $countries[$countryKey]['states'][$stateName] = $state;

                        foreach ($cities as $city) {
                            $cityName = str_replace(' ', '' , strtolower($city['name']));

                            $countries[$countryKey]['states'][$stateName]['cities'][$cityName] = $city;
                        }
                    }
                }
            }
        }

        foreach ($countries as $countryKey => &$country) {
            $this->localContent->write($this->sourceDir . $countryKey . '.json', JSON::encode($country));

            unset($country['states']);
        }


        $this->localContent->write($this->sourceDir . 'AllCountries.json', JSON::encode($countries));

        $this->packagesData->responseMessage = 'Data Extracted Successfully!';
    }

    protected function mergeCountryData()
    {
        try {
            $allCountries = Json::decode($this->localContent->read($this->sourceDir . 'ip2location/AllCountries.json'), true);

            foreach ($allCountries as $country) {
                if ($this->localContent->fileExists($this->sourceDir . $country . '.json')) {
                    $countryKey = $country;

                    $countryData = Json::decode($this->localContent->read($this->sourceDir . $country . '.json'), true);
                    $iplocationData = Json::decode($this->localContent->read($this->sourceDir . 'ip2location/' . $country . '.json'), true);

                    $country = array_replace_recursive($countryData, $iplocationData);

                    // $country['errors'] = [];
                    // $error = false;

                    // foreach ($country['states'] as $stateKey => $state) {
                    //     if (!isset($state['name'])) {
                    //         $error = true;

                    //         array_push($country['errors'], $stateKey);
                    //     }
                    // }

                    // if ($error) {
                    //     $countryKey = $countryKey . '_ERROR';
                    // }

                    $this->localContent->write($this->sourceDir . $countryKey . '.json', JSON::encode($country));

                    $this->compressZip($countryKey);
                } else {
                    // var_dump($country);
                }
            }

            $this->packagesData->responseMessage = 'Data Extracted & Merged Successfully!';
        } catch (FilesystemException | UnableToReadFile $exception) {
            throw $exception;
        }
    }

    protected function compressZip($sourceFile)
    {
        $zip = new \ZipArchive;

        $zip->open(base_path($this->sourceDir . $sourceFile . '.zip'), $zip::CREATE);

        $zip->addFile(base_path($this->sourceDir . $sourceFile . '.json'));

        $zip->close();
    }

    public function extractTZData()
    {
        $wikiTz = [];

        include('vendor/Simplehtmldom.php');

        $response = $this->remoteWebContent->get($this->tzLink)->getBody()->getContents();

        $html = str_get_html($response);

        $table = $html->find('table.wikitable tbody');

        if ($table[0] && $table[0]->children) {
            foreach ($table[0]->children as $tr) {
                if (trim($tr->children[4]->plaintext) === 'Canonical') {
                    $zoneName = trim($tr->children[2]->plaintext);
                    $zoneKey = strtolower(str_replace('/', '', $zoneName));

                    $wikiTz[$zoneKey]['zoneName'] = trim($tr->children[2]->plaintext);
                    $wikiTz[$zoneKey]['gmtOffsetName'] = 'UTC' . trim($tr->children[5]->plaintext);
                    $wikiTz[$zoneKey]['gmtOffset'] = $this->getGMTOffset(trim($tr->children[5]->plaintext));
                    $wikiTz[$zoneKey]['gmtOffsetNameDST'] = 'UTC' . trim($tr->children[6]->plaintext);
                    $wikiTz[$zoneKey]['gmtOffsetDST'] = $this->getGMTOffset(trim($tr->children[6]->plaintext));
                }
            }
        }

        $allCountries = Json::decode($this->localContent->read($this->sourceDir . 'AllCountries.json'), true);

        foreach ($allCountries as $country) {
            if (isset($country['timezones']) && count($country['timezones']) > 0) {
                foreach ($country['timezones'] as $tzKey => $tz) {
                    $tzName = strtolower(str_replace('/', '', $tz['zoneName']));

                    if (isset($wikiTz[$tzName])) {
                        $wikiTz[$tzName] = array_replace($tz, $wikiTz[$tzName]);
                    } else {
                        $wikiTz[$tzName] = $tz;
                    }
                }
            }
        }

        $this->localContent->write($this->sourceDir . 'TimeZones.json', JSON::encode($wikiTz));

        $this->packagesData->responseMessage = 'Data Extracted Successfully!';
    }
}