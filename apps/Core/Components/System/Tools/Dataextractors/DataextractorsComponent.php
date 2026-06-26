<?php

namespace Apps\Core\Components\System\Tools\Dataextractors;

use System\Base\BaseComponent;

class DataextractorsComponent extends BaseComponent
{
    public function initialize()
    {
        //
    }

    public function viewAction()
    {
        $this->view->countries = $this->basepackages->geoCountries->getAll()->geoCountries;
    }

    public function processAction()
    {
        $this->requestIsPost();

        if ($this->basepackages->progress->checkProgressFile()) {
            $this->basepackages->progress->deleteProgressFile();
        }

        if (!$this->registerProgressMethods()) {
            $this->addResponse('No Methods Selected', 1);

            return;
        }

        try {
            if (isset($this->postData()['process']) && $this->postData()['process'] === 'geo') {
                $this->basepackages->dataExtractors->downloadGeoCountriesData();
                $this->basepackages->dataExtractors->downloadGeoPostcodeData();
                $this->basepackages->dataExtractors->processGeoCountriesData();
                $this->basepackages->dataExtractors->processGeoDataToDb();
            }

            if (isset($this->postData()['timezone']) && $this->postData()['timezone'] == 'true') {
                $this->basepackages->dataExtractors->downloadTimezoneData();
                $this->basepackages->dataExtractors->processTimezoneData();
            }

            if (isset($this->postData()['ip']) && $this->postData()['ip'] == 'true') {
                // $this->basepackages->dataExtractors->downloadGeoIpv4Data();
                // $this->basepackages->dataExtractors->unzipGeoIpv4Data();
                // $this->basepackages->dataExtractors->processGeoIpv4Data();
                // $this->basepackages->dataExtractors->downloadGeoIpv6Data();
                // $this->basepackages->dataExtractors->unzipGeoIpv6Data();
                // $this->basepackages->dataExtractors->processGeoIpv6Data();
                // $this->basepackages->dataExtractors->mergeGeoIpData();
            }

            $this->addResponse(
                $this->basepackages->dataExtractors->packagesData->responseMessage,
                $this->basepackages->dataExtractors->packagesData->responseCode
            );
        } catch (\throwable $e) {
            $this->basepackages->progress->preCheckComplete(false);

            $this->basepackages->progress->resetProgress();

            $this->addResponse($e->getMessage(), 1);
        }
    }

    protected function registerProgressMethods()
    {
        $methods = [];

        if (isset($this->postData()['process']) && $this->postData()['process'] === 'geo') {
            $methods = array_merge($methods,
                [
                    [
                        'method'    => 'downloadGeoCountriesData',
                        'text'      => 'Download Geo Location Countries Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'downloadGeoPostcodeData',
                        'text'      => 'Download Geo Location Postcode Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'processGeoCountriesData',
                        'text'      => 'Process Geo Location Countries & Postcode Data...'
                    ],
                    [
                        'method'    => 'processGeoDataToDb',
                        'text'      => 'Process Geo Location Data to Database...'
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

        $this->basepackages->progress->registerMethods($methods);

        return true;
    }
}