<?php

namespace Apps\Core\Components\System\Tools\Dataextractors;

use System\Base\BaseComponent;

class DataextractorsComponent extends BaseComponent
{
    public $dataExtractors;

    public function initialize()
    {
        $this->dataExtractors = $this->basepackages->dataExtractors;

        return $this;
    }

    public function viewAction()
    {
        $this->view->countries = $this->basepackages->geoCountries->getAll()->geoCountries;
    }

    public function processAction($data = null)
    {
        if (!isset($data)) {
            $this->requestIsPost();

            $data = $this->postData();
        }

        if (!isset($data['process'])) {
            $this->addResponse('Nothing to process', 1);

            return;
        }

        if ($this->basepackages->progress->checkProgressFile('dataextractors')) {
            $this->basepackages->progress->deleteProgressFile(true);
        }

        if (!$this->registerProgressMethods($data)) {
            $this->addResponse('Nothing Selected', 1);

            return;
        }

        try {
            if ($data['process'] === 'geo') {
                if (isset($data['countries'])) {
                    $this->dataExtractors->downloadGeoCountriesData();
                    $this->dataExtractors->downloadGeoPostcodeData();
                    $this->dataExtractors->processDownloadedGeoCountriesData($data);
                }

                if (isset($data['update_countries_regions']) && $data['update_countries_regions'] == 'true') {
                    $this->dataExtractors->processGeoCountriesData($data);
                }

                if (isset($data['countries'])) {
                    $this->dataExtractors->processCountryStatesCititesPostcodesData($data);
                }

                if (isset($data['timezone']) && $data['timezone'] == 'true') {
                    $this->dataExtractors->downloadTimezoneData();
                    $this->dataExtractors->processTimezoneData();
                }
            } else if ($data['process'] === 'ip2location') {
                //
            } else if ($data['process'] === 'dictionary') {
                $this->dataExtractors->downloadDictionaryData();
                $this->dataExtractors->processDictionaryData();
            }


            if (isset($data['ip']) && $data['ip'] == 'true') {
                // $this->dataExtractors->downloadGeoIpv4Data();
                // $this->dataExtractors->unzipGeoIpv4Data();
                // $this->dataExtractors->processGeoIpv4Data();
                // $this->dataExtractors->downloadGeoIpv6Data();
                // $this->dataExtractors->unzipGeoIpv6Data();
                // $this->dataExtractors->processGeoIpv6Data();
                // $this->dataExtractors->mergeGeoIpData();
            }

            $this->addResponse(
                $this->dataExtractors->packagesData->responseMessage,
                $this->dataExtractors->packagesData->responseCode,
                $this->dataExtractors->packagesData->responseData ?? []
            );
        } catch (\throwable $e) {
            trace([$e]);
            $this->basepackages->progress->preCheckComplete(false);

            $this->basepackages->progress->resetProgress();

            $this->addResponse($e->getMessage(), 1);
        }
    }

    protected function registerProgressMethods($data)
    {
        $methods = [];

        if ($data['process'] === 'geo') {
            if (isset($data['countries'])) {
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
                            'method'    => 'processDownloadedGeoCountriesData',
                            'text'      => 'Process Downloaded Geo Location Countries & Postcode Data...'
                        ]
                    ]
                );
            }

            if (isset($data['update_countries_regions']) && $data['update_countries_regions'] == 'true') {
                $methods = array_merge($methods,
                    [
                        [
                            'method'    => 'processGeoCountriesData',
                            'text'      => 'Update Countries & Regions Data...',
                        ]
                    ]
                );
            }

            if (isset($data['countries'])) {
                $methods = array_merge($methods,
                    [
                        [
                            'method'    => 'processCountryStatesCititesPostcodesData',
                            'text'      => 'Process Country States, Cities & Postcode Data ...',
                            'steps'     => true
                        ]
                    ]
                );
            }

            if (isset($data['timezone']) && $data['timezone'] == 'true') {
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
        } else if ($data['process'] === 'ip2location') {
            if (isset($data['ip']) && $data['ip'] == 'true') {
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
        } else if ($data['process'] === 'dictionary') {
            $methods = array_merge($methods,
                [
                    [
                        'method'    => 'downloadDictionaryData',
                        'text'      => 'Download Dictionary Data...',
                        'remoteWeb' => true
                    ],
                    [
                        'method'    => 'processDictionaryData',
                        'text'      => 'Process Downloaded Dictionary Data...'
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