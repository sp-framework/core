<?php

namespace Apps\Core\Components\Devtools\Extractgeodata;

use Apps\Core\Packages\Devtools\GeoExtractData\DevtoolsGeoExtractData;
use System\Base\BaseComponent;

class ExtractgeodataComponent extends BaseComponent
{
    protected $geoExtractDataPackage;

    public function initialize()
    {
        $this->geoExtractDataPackage = new DevtoolsGeoExtractData;
    }

    public function viewAction()
    {
        //
    }

    //https://github.com/dr5hn/countries-states-cities-database
    //https://github.com/dr5hn/countries-states-cities-database/raw/master/countries%2Bstates%2Bcities.json
    //To update get table from wikipedia link - https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
    //Ip2Location data from ip2location.com (lite version)
    public function processAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->basepackages->progress->checkProgressFile()) {
                $this->basepackages->progress->deleteProgressFile();
            }

            if (!$this->registerProgressMethods()) {
                $this->addResponse('No Methods Selected', 1);

                return;
            }

            if (isset($this->postData()['geo']) && $this->postData()['geo'] == 'true') {
                $this->geoExtractDataPackage->downloadGeoData();
                $this->geoExtractDataPackage->processGeoData();
            }

            if (isset($this->postData()['timezone']) && $this->postData()['timezone'] == 'true') {
                $this->geoExtractDataPackage->downloadTimezoneData();
                $this->geoExtractDataPackage->processTimezoneData();
            }

            if (isset($this->postData()['ip']) && $this->postData()['ip'] == 'true') {
                $this->geoExtractDataPackage->downloadGeoIpv4Data();
                $this->geoExtractDataPackage->unzipGeoIpv4Data();
                $this->geoExtractDataPackage->processGeoIpv4Data();
                $this->geoExtractDataPackage->downloadGeoIpv6Data();
                $this->geoExtractDataPackage->unzipGeoIpv6Data();
                $this->geoExtractDataPackage->processGeoIpv6Data();
                $this->geoExtractDataPackage->mergeGeoIpData();
            }

            $this->geoExtractDataPackage->zipData();

            $this->addResponse(
                $this->geoExtractDataPackage->packagesData->responseMessage,
                $this->geoExtractDataPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    protected function registerProgressMethods()
    {
        $methods = [];

        if (isset($this->postData()['geo']) && $this->postData()['geo'] == 'true') {
            $methods = array_merge($methods,
                [
                    [
                        'method'    => 'downloadGeoData',
                        'text'      => 'Download Geo Location Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'processGeoData',
                        'text'      => 'Process Geo Location Data...'
                    ]
                ]
            );
        }

        if (isset($this->postData()['timezone']) && $this->postData()['timezone'] == 'true') {
            $methods = array_merge($methods,
                [
                    [
                        'method'    => 'downloadTimezoneData',
                        'text'      => 'Download Timezone Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'processTimezoneData',
                        'text'      => 'Process Timezone Data...'
                    ]
                ]
            );
        }

        if (isset($this->postData()['ip']) && $this->postData()['ip'] == 'true') {
            $methods = array_merge($methods,
                [
                    [
                        'method'    => 'downloadGeoIpv4Data',
                        'text'      => 'Download Geo Location IPv4 Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'unzipGeoIpv4Data',
                        'text'      => 'Unzip Geo Location IPv4 Data...',
                    ],
                    [
                        'method'    => 'processGeoIpv4Data',
                        'text'      => 'Process Geo Location IPv4 Data. This will take a while...',
                    ],
                    [
                        'method'    => 'downloadGeoIpv6Data',
                        'text'      => 'Download Geo Location IPv6 Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'unzipGeoIpv6Data',
                        'text'      => 'Unzip Geo Location IPv6 Data...',
                    ],
                    [
                        'method'    => 'processGeoIpv6Data',
                        'text'      => 'Process Geo Location IPv6 Data. This will take a while...',
                    ],
                    [
                        'method'    => 'mergeGeoIpData',
                        'text'      => 'Merge Geo Location IP Data. This will  take a while...',
                    ]
                ]
            );
        }

        if (count($methods) === 0) {
            return false;
        }

        $methods = array_merge($methods,
            [
                [
                    'method'    => 'zipData',
                    'text'      => 'Zip Geo Location Data...',
                ]
            ]
        );

        $this->basepackages->progress->registerMethods($methods);

        return true;
    }
}