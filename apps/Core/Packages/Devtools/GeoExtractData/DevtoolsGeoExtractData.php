<?php

namespace Apps\Core\Packages\Devtools\GeoExtractData;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use System\Base\BasePackage;

class DevtoolsGeoExtractData extends BasePackage
{
    protected $sourceDir = 'apps/Core/Packages/Devtools/GeoExtractData/Data/';

    protected $sourceFile;

    protected $trackCounter = 0;

    public $method;

    public function onConstruct()
    {
        if (!is_dir(base_path($this->sourceDir))) {
            if (!mkdir(base_path($this->sourceDir), 0777, true)) {
                return false;
            }
        }

        parent::onConstruct();
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

    protected function downloadGeoData()
    {
        $this->method = 'downloadGeoData';

        return $this->downloadData(
            'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/countries%2Bstates%2Bcities.json',
            base_path('apps/Core/Packages/Devtools/GeoExtractData/Data/countries+states+cities.json')
        );
    }

    protected function processGeoData()
    {
        // /etc/apache2.conf - Change the timeout to 1800 else you will get Gateway Timeout, revert back when done to 300 (5 mins)
        // Timeout 1800

        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 1800) {
            set_time_limit(1800);
        }

        //Increase memory_limit to 2G as the process takes a bit of memory to process the array.
        if ((int) ini_get('memory_limit') < 2048) {
            ini_set('memory_limit', '2048M');
        }

        $countries = [];

        try {
            if ($this->localContent->fileExists($this->sourceDir . 'countries+states+cities.json')) {
                $this->sourceFile = $this->helper->decode($this->localContent->read($this->sourceDir . 'countries+states+cities.json'), true);
            }
        } catch (FilesystemException | UnableToReadFile | \throwable $e) {
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
            $this->localContent->write($this->sourceDir . $countryKey . '.json', $this->helper->encode($country));

            unset($country['states']);
        }

        $this->localContent->write($this->sourceDir . 'AllCountries.json', $this->helper->encode($countries));

        return true;
    }

    protected function zipData()
    {
        try {
            $allCountries = $this->helper->decode($this->localContent->read($this->sourceDir . 'AllCountries.json'), true);

            foreach ($allCountries as $country) {
                $this->compressZip($country['iso2']);
            }

            return true;
        } catch (FilesystemException | UnableToReadFile | \throwable $exception) {
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

        $zip->close();

        return true;
    }

    protected function compressZip($sourceFile)
    {
        $zip = new \ZipArchive;

        $zip->open(base_path($this->sourceDir . $sourceFile . '.zip'), $zip::CREATE);

        $zip->addFile(base_path($this->sourceDir . $sourceFile . '.json'), $sourceFile . '.json');

        $zip->close();
    }

    protected function downloadTimezoneData()
    {
        $this->method = 'downloadTimezoneData';

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
                if (isset($tr->children[4]) && trim($tr->children[4]->plaintext) === 'Canonical') {
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

        $allCountries = $this->helper->decode($this->localContent->read($this->sourceDir . 'AllCountries.json'), true);

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

        try {
            $this->localContent->write($this->sourceDir . 'TimeZones.json', $this->helper->encode($wikiTz));
        } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
            throw $e;
        }

        return true;
    }

    protected function downloadGeoIpv4Data()
    {
        $this->method = 'downloadGeoIpv4Data';

        return $this->downloadData(
            'https://www.ip2location.com/download/?token=' . $this->postData()['token'] . '&file=DB11LITECSV',
            base_path('apps/Core/Packages/Devtools/GeoExtractData/Data/IP2LOCATION-LITE-DB11.CSV.ZIP')
        );
    }

    protected function unzipGeoIpv4Data()
    {
        return $this->extractZip('apps/Core/Packages/Devtools/GeoExtractData/Data/IP2LOCATION-LITE-DB11.CSV.ZIP');
    }

    protected function processGeoIpv4Data()
    {
        $this->sourceFile = Reader::createFromPath(base_path($this->sourceDir . 'IP2LOCATION-LITE-DB11.CSV'));

        return $this->processCSV('ip2locationv4');
    }

    protected function downloadGeoIpv6Data()
    {
        $this->method = 'downloadGeoIpv6Data';

        return $this->downloadData(
            'https://www.ip2location.com/download/?token=' . $this->postData()['token'] . '&file=DB11LITECSVIPV6',
            base_path('apps/Core/Packages/Devtools/GeoExtractData/Data/IP2LOCATION-LITE-DB11.IPV6.CSV.ZIP')
        );
    }

    protected function unzipGeoIpv6Data()
    {
        return $this->extractZip('apps/Core/Packages/Devtools/GeoExtractData/Data/IP2LOCATION-LITE-DB11.IPV6.CSV.ZIP');
    }

    protected function processGeoIpv6Data()
    {
        $this->sourceFile = Reader::createFromPath(base_path($this->sourceDir . 'IP2LOCATION-LITE-DB11.IPV6.CSV'));

        return $this->processCSV('ip2locationv6');
    }

    protected function processCSV($ipType)
    {
        // /etc/apache2.conf - Change the timeout to 1800 else you will get Gateway Timeout, revert back when done to 300 (5 mins)
        // Timeout 1800

        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 1800) {
            set_time_limit(1800);
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
                isset($countries[$record[2]]['states'][$stateName]['cities'][$cityName][$ipType])
            ) {
                $ipRanges = array_merge($countries[$record[2]]['states'][$stateName]['cities'][$cityName][$ipType], [$record[0], $record[1]]);
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
                    $ipType             => $ipRanges
                ];
        }

        foreach ($countries as $countryKey => $country) {
            $this->localContent->write($this->sourceDir . 'ip2location/' . $ipType . '/' . $countryKey . '.json', $this->helper->encode($country));

            array_push($countryKeys, $countryKey);
        }

        $this->localContent->write($this->sourceDir . 'ip2location/AllCountries.json', $this->helper->encode($countryKeys));

        return true;
    }

    protected function mergeGeoIpData()
    {
        // /etc/apache2.conf - Change the timeout to 1800 else you will get Gateway Timeout, revert back when done to 300 (5 mins)
        // Timeout 1800

        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 1800) {
            set_time_limit(1800);
        }

        //Increase memory_limit to 2G as the process takes a bit of memory to process the array.
        if ((int) ini_get('memory_limit') < 2048) {
            ini_set('memory_limit', '2048M');
        }

        try {
            $allCountries = $this->helper->decode($this->localContent->read($this->sourceDir . 'ip2location/AllCountries.json'), true);

            foreach ($allCountries as $country) {
                $countryData = [];

                if ($this->localContent->fileExists($this->sourceDir . $country . '.json')) {
                    $countryData = $this->helper->decode($this->localContent->read($this->sourceDir . $country . '.json'), true);

                    $iplocationv4Data = [];
                    if ($this->localContent->fileExists($this->sourceDir . 'ip2location/ip2locationv4/' . $country . '.json')) {
                        $iplocationv4Data = $this->helper->decode($this->localContent->read($this->sourceDir . 'ip2location/ip2locationv4/' . $country . '.json'), true);
                    }

                    $iplocationv6Data = [];
                    if ($this->localContent->fileExists($this->sourceDir . 'ip2location/ip2locationv6/' . $country . '.json')) {
                        $iplocationv6Data = $this->helper->decode($this->localContent->read($this->sourceDir . 'ip2location/ip2locationv6/' . $country . '.json'), true);
                    }

                    $countryGeoData = array_replace_recursive($countryData, $iplocationv4Data, $iplocationv6Data);

                    $this->localContent->write($this->sourceDir . $country . '.json', $this->helper->encode($countryGeoData));
                }
            }

            return true;
        } catch (FilesystemException | UnableToReadFile | UnableToWriteFile | \throwable $exception) {
            throw $exception;
        }
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
                'sink'              => $sink
            ]
        );

        $this->trackCounter = 0;

        if ($download->getStatusCode() === 200) {
            return true;
        }

        $this->addResponse('Download resulted in : ' . $download->getStatusCode(), 1);

        return false;
    }
}