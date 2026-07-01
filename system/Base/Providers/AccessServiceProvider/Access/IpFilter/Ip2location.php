<?php

namespace System\Base\Providers\AccessServiceProvider\Access\IpFilter;

use IP2LocationIO\Configuration;
use IP2LocationIO\IPGeolocation;
use IP2Location\Database;
use IP2Location\IpTools;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\Providers\AccessServiceProvider\Access\IpFilter\PackagesData;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationCities;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationCountries;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationStates;

class Ip2location
{
    public $packagesData;

    public $ipTools;

    public $ipGeoLocation;

    public $ipFilterFiltersIp2locationStore;

    protected $dataPath = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Ip2location/';

    protected $varPath = 'var/dataextractors/ip2location/';

    protected $ipFilter;

    protected $ipFilterSettings;

    public function __construct($ipFilter)
    {
        $this->packagesData = new PackagesData;

        $this->ipFilter = $ipFilter;

        $this->ipFilterSettings = $this->ipFilter->getIpFilterSettings();

        $this->ipTools = new IpTools;

        if (isset($this->ipFilterSettings['ip2location_io_api_key']) &&
            $this->ipFilterSettings['ip2location_io_api_key'] !== ''
        ) {
            $this->ipGeoLocation = new IPGeolocation(new Configuration($this->ipFilterSettings['ip2location_io_api_key']));
        }
    }

    public function getIpDetailsFromIp2locationBIN($ip)
    {
        if (!$this->checkIPIsPublic($ip)) {
            return false;
        }

        try {
            $ip2locationBin =
                new \IP2Location\Database(
                    base_path($this->varPath . $this->ipFilterSettings['ip2location_bin_file_code'] . '.BIN'),
                    constant('\IP2Location\Database::' . $this->ipFilterSettings['ip2location_bin_access_mode'])
                );
        } catch (\throwable $e) {
            trace([$e]);
            //Log here
            if (str_contains($e->getMessage(), 'exist')) {
                $this->addResponse('Bin file does not exist, please download bin file first to check in bin file.', 1);
            } else {
                $this->addResponse($e->getMessage(), 1);
            }

            return false;
        }

        $ipDetailsArr = $ip2locationBin->lookup($ip, \IP2Location\Database::ALL);
        trace([$ipDetailsArr]);
        if ($ipDetailsArr) {
            $ipDetails['address'] = $ip;
            $ipDetails['country_code'] = $ipDetailsArr['countryCode'];
            $ipDetails['country_name'] = $ipDetailsArr['countryName'];
            $ipDetails['region_name'] = $ipDetailsArr['regionName'];
            $ipDetails['city_name'] = $ipDetailsArr['cityName'];
            $ipDetails['is_proxy'] = false;
            $ipDetails['proxy_type'] = '-';
            if ($ipProxyDetails = $this->getIpDetailsFromIp2locationProxyBIN($ip)) {
                $ipDetails = array_merge($ipDetails, $ipProxyDetails);
            }

            $this->addResponse('Details for IP: ' . $ip . ' retrieved successfully using BIN file.', 0, ['ip_details' => $ipDetails]);

            return $ipDetails;
        }

        $this->addResponse('Details for IP: ' . $ip . ' not available in the BIN file. Please search API.', 2);

        return false;
    }

    public function getIpDetailsFromIp2locationProxyBIN($ip)
    {
        if (!$this->checkIPIsPublic($ip)) {
            return false;
        }

        try {
            $ip2locationProxyBin =
                new \IP2Proxy\Database(
                    base_path($this->varPath . $this->ipFilterSettings['ip2location_proxy_bin_file_code'] . '.BIN'),
                    constant('\IP2Proxy\Database::' . $this->ipFilterSettings['ip2location_proxy_bin_access_mode'])
                );

            $ipDetailsArr = $ip2locationProxyBin->lookup($ip, \IP2Proxy\Database::ALL);
        } catch (\throwable $e) {
            //Log here
            if (str_contains($e->getMessage(), 'exist')) {
                $this->addResponse('Bin file does not exist, please download bin file first to check in bin file.', 1);
            } else {
                $this->addResponse($e->getMessage(), 1);
            }

            return false;
        }

        if ($ipDetailsArr && isset($ipDetailsArr['countryCode']) && $ipDetailsArr['countryCode'] !== '-') {
            $ipDetails['address'] = $ip;
            $ipDetails['is_proxy'] = $ipDetailsArr['isProxy'];
            $ipDetails['proxy_type'] = $ipDetailsArr['proxyType'];

            $this->addResponse('Details for IP: ' . $ip . ' retrieved successfully using Proxy BIN file.', 0, ['ip_details' => $ipDetails]);

            return $ipDetails;
        }

        $this->addResponse('Details for IP: ' . $ip . ' not available in the Proxy BIN file. Please search API.', 2);

        return false;
    }

    public function getIpDetailsFromIp2locationAPI($ip)
    {
        if (!$this->checkIPIsPublic($ip)) {
            return false;
        }

        $ff = null;
        $db = null;

        try {
            $ff = $this->ipFilter->getDi()->getShared('ff');
            $db = $this->ipFilter->getDi()->getShared('db');
            $logger = $this->ipFilter->getDi()->getShared('logger');
        } catch (\throwable $e) {
            throw $e;
        }

        if ($ff) {
            $ip2locationStore = $ff->store('service_provider_access_ip_filters_ip2location');

            $ipFilterFiltersIp2locationStoreEntry = $ip2locationStore->findBy(['address', '=', $ip]);
        } else if ($db) {
            $ipFilterFiltersIp2locationStoreEntry =
                $db->fetchAll(
                    "SELECT * FROM service_provider_access_ip_filters_ip2location WHERE address = :address",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        'address'  => $ip
                    ]
                );
        }
        // trace([$ipFilterFiltersIp2locationStoreEntry]);
        if ($ipFilterFiltersIp2locationStoreEntry && isset($ipFilterFiltersIp2locationStoreEntry[0])) {
            $this->addResponse('Details for IP: ' . $ip . ' retrieved successfully using ip2location local database.', 0, ['ip_details' => $ipFilterFiltersIp2locationStoreEntry[0]]);

            return $ipFilterFiltersIp2locationStoreEntry[0];
        }

        if ($this->ipGeoLocation) {
            try {
                $apiCallResponse = $this->ipGeoLocation->lookup($ip, $this->ipFilterSettings['ip2location_io_api_language']);

                if ($apiCallResponse) {
                    $apiCallResponse = json_decode(json_encode($apiCallResponse), true);

                    $ipDetails['address'] = $apiCallResponse['ip'];
                    if ($this->ipTools->isIpv4($ip)) {
                        $ipDetails['decimal'] = (int) $this->ipTools->ipv4ToDecimal($ip);
                    } else if ($this->ipTools->isIpv6($ip)) {
                        $ipDetails['decimal'] = (int) $this->ipTools->ipv6ToDecimal($ip);
                    }
                    $ipDetails['country_code'] = $apiCallResponse['country_code'];
                    $ipDetails['region_name'] = $apiCallResponse['region_name'];
                    $ipDetails['city_name'] = $apiCallResponse['city_name'];
                    $ipDetails['is_proxy'] = $apiCallResponse['is_proxy'];
                    $ipDetails['proxy_type'] = '-';
                    if (isset($apiCallResponse['proxy']) && isset($apiCallResponse['proxy']['proxy_type'])) {
                        $ipDetails['proxy_type'] = $apiCallResponse['proxy']['proxy_type'];
                    }

                    if ($ff) {
                        $ipDetails = $ip2locationStore->insert($ipDetails);
                    } else if ($db) {
                        $ipDetails = $db->insertAsDict('service_provider_access_ip_filters_ip2location', $ipDetails);
                    }

                    $this->addResponse('Details for IP: ' . $ip . ' retrieved successfully using API.', 0, ['ip_details' => $ipDetails]);

                    return $apiCallResponse;
                }
            } catch (\throwable $e) {
                trace([$e]);
                //Log here
                if ($this->ipFilterSettings['debug_filters'] === true) {
                    $logger->logIpFilters->error(json_encode(['status' => 'ERROR_IP2LOCATION', 'error' => $e->getMessage()]));
                }

                $this->addResponse($e->getMessage(), 1);
            }
        } else {
            if ($this->ipFilterSettings['debug_filters'] === true) {
                $logger->logIpFilters->error(json_encode(['status' => 'ERROR_IP2LOCATION', 'error' => $e->getMessage()]));
            }

            $this->addResponse('Lookup is using io API and io API keys are not set!', 1);
        }

        return false;
    }

    public function checkIPIsPublic($ip)
    {
        if ($this->ipFilter->filters->validateIP($ip)) {
            $ipv6 = false;

            if (str_contains($ip, ':')) {
                $ipv6 = true;
            }

            $isPublic = filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                ($ipv6 ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4) | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
            );

            if (!$isPublic) {
                $this->addResponse('IP Address : ' . $ip . ' is from a private range of IP addresses!', 2);

                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function getAllCountries()
    {
        $ff = null;
        $db = null;

        try {
            $ff = $this->ipFilter->getDi()->getShared('ff');
            $db = $this->ipFilter->getDi()->getShared('db');
        } catch (\throwable $e) {
            throw $e;
        }

        $countries = [];

        if ($ff) {
            $ip2locationCountriesStore = $ff->store('service_provider_access_ip_filters_ip2location_countries');

            $countries = $ip2locationCountriesStore->findAll();
        } else if ($db) {
            $countries =
                $db->fetchAll(
                    "SELECT * FROM service_provider_access_ip_filters_ip2location_countries",
                    \Phalcon\Db\Enum::FETCH_ASSOC
                );
        }

        if (count($countries) === 0) {
            $localContent = $this->ipFilter->getDi()->getShared('localContent');

            try {
                if ($localContent->fileExists($this->dataPath . 'AllCountries.json')) {
                    $countries = json_decode($localContent->read($this->dataPath . 'AllCountries.json'), true);
                }
            } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
                $countries = [];//We ignore and get countries list via Geo Counties
            }
        }

        return $countries;
    }

    public function getIp2locationInfo()
    {
        try {
            $localContent = $this->ipFilter->getDi()->getShared('localContent');

            if ($localContent->fileExists($this->varPath . 'info.json')) {
                return json_decode($localContent->read($this->varPath . 'info.json'), true);
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);
        }

        return false;
    }

    public function searchCountries(string $countryQueryString)
    {
        $ff = null;
        $db = null;

        try {
            $ff = $this->ipFilter->getDi()->getShared('ff');
            $db = $this->ipFilter->getDi()->getShared('db');
        } catch (\throwable $e) {
            throw $e;
        }

        $countries = [];

        if ($ff) {
            $ip2locationCountriesStore = $ff->store('service_provider_access_ip_filters_ip2location_countries');

            $countries = $ip2locationCountriesStore->findBy(['name', 'like', '%' . $countryQueryString . '%']);
        } else if ($db) {
            $countries =
                $db->fetchAll(
                    "SELECT * FROM service_provider_access_ip_filters_ip2location_countries WHERE name LIKE :name",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        'name'  => $countryQueryString
                    ]
                );
        }

        $this->addResponse('Ok', 0, ['countries' => $countries]);

        return $countries;
    }

    public function searchStates($countryId, string $stateQueryString)
    {
        $ff = null;
        $db = null;

        try {
            $ff = $this->ipFilter->getDi()->getShared('ff');
            $db = $this->ipFilter->getDi()->getShared('db');
        } catch (\throwable $e) {
            throw $e;
        }

        $countries = [];
        $states = [];

        if ($ff) {
            $ip2locationStatesStore = $ff->store('service_provider_access_ip_filters_ip2location_states');

            $searchStates = $ip2locationStatesStore->findBy([['country_id', '=', (int) $countryId],['name', 'like', '%' . $stateQueryString . '%']]);
        } else if ($db) {
            $searchStates =
                $db->fetchAll(
                    "SELECT * FROM service_provider_access_ip_filters_ip2location_states WHERE name LIKE :name",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        'name'  => $stateQueryString
                    ]
                );
        }

        if (isset($searchStates) && count($searchStates) > 0) {
            foreach ($searchStates as $stateKey => $stateValue) {
                if (!isset($countries[$stateValue['country_id']])) {
                    if ($ff) {
                        $ip2locationCountriesStore = $ff->store('service_provider_access_ip_filters_ip2location_countries');

                        $searchCountry = $ip2locationCountriesStore->findBy(['id', '=', $stateValue['country_id']]);
                    } else if ($db) {
                        $searchCountry =
                            $db->fetchAll(
                                "SELECT * FROM service_provider_access_ip_filters_ip2location_countries WHERE id = :id",
                                \Phalcon\Db\Enum::FETCH_ASSOC,
                                [
                                    'id'  => $stateValue['country_id']
                                ]
                            );
                    }

                    if (isset($searchCountry[0])) {
                        $countries[$stateValue['country_id']] = $searchCountry[0];
                    } else {
                        continue;
                    }
                }

                $states[$stateKey] = $stateValue;
                $states[$stateKey]['country_id'] = $countries[$stateValue['country_id']]['id'];
                $states[$stateKey]['country_iso2'] = $countries[$stateValue['country_id']]['iso2'];
                $states[$stateKey]['country_name'] = $countries[$stateValue['country_id']]['name'];
            }
        }

        $this->addResponse('Ok', 0, ['states' => $states]);

        return $states;
    }

    public function searchCities($countryId, string $cityQueryString)
    {
        $ff = null;
        $db = null;

        try {
            $ff = $this->ipFilter->getDi()->getShared('ff');
            $db = $this->ipFilter->getDi()->getShared('db');
        } catch (\throwable $e) {
            throw $e;
        }

        $countries = [];
        $states = [];
        $cities = [];

        if ($ff) {
            $ip2locationCitiesStore = $ff->store('service_provider_access_ip_filters_ip2location_cities');

            $searchCities = $ip2locationCitiesStore->findBy([['country_id', '=', (int) $countryId],['name', 'like', '%' . $cityQueryString . '%']]);
        } else if ($db) {
            $searchCities =
                $db->fetchAll(
                    "SELECT * FROM service_provider_access_ip_filters_ip2location_cities WHERE name LIKE :name",
                    \Phalcon\Db\Enum::FETCH_ASSOC,
                    [
                        'name'  => $cityQueryString
                    ]
                );
        }

        if (isset($searchCities) && count($searchCities) > 0) {
            foreach ($searchCities as $cityKey => $cityValue) {
                if (!isset($countries[$cityValue['country_id']])) {
                    if ($ff) {
                        $ip2locationCountriesStore = $ff->store('service_provider_access_ip_filters_ip2location_countries');

                        $searchCountry = $ip2locationCountriesStore->findBy(['id', '=', $cityValue['country_id']]);
                    } else if ($db) {
                        $searchCountry =
                            $db->fetchAll(
                                "SELECT * FROM service_provider_access_ip_filters_ip2location_countries WHERE id = :id",
                                \Phalcon\Db\Enum::FETCH_ASSOC,
                                [
                                    'id'  => $cityValue['country_id']
                                ]
                            );
                    }

                    if (isset($searchCountry[0])) {
                        $countries[$cityValue['country_id']] = $searchCountry[0];
                    } else {
                        continue;
                    }
                }

                if (!isset($states[$cityValue['state_id']])) {
                    if ($ff) {
                        $ip2locationStatesStore = $ff->store('service_provider_access_ip_filters_ip2location_states');

                        $searchState = $ip2locationStatesStore->findBy(['id', '=', $cityValue['state_id']]);
                    } else if ($db) {
                        $searchState =
                            $db->fetchAll(
                                "SELECT * FROM service_provider_access_ip_filters_ip2location_states WHERE id = :id",
                                \Phalcon\Db\Enum::FETCH_ASSOC,
                                [
                                    'id'  => $cityValue['state_id']
                                ]
                            );
                    }

                    if (isset($searchState[0])) {
                        $states[$cityValue['state_id']] = $searchState[0];
                    } else {
                        continue;
                    }
                }

                $cities[$cityKey] = $cityValue;
                $cities[$cityKey]['state_id'] = $states[$cityValue['state_id']]['id'];
                $cities[$cityKey]['state_name'] = $states[$cityValue['state_id']]['name'];
                $cities[$cityKey]['country_id'] = $countries[$cityValue['country_id']]['id'];
                $cities[$cityKey]['country_iso2'] = $countries[$cityValue['country_id']]['iso2'];
                $cities[$cityKey]['country_name'] = $countries[$cityValue['country_id']]['name'];
            }
        }

        $this->addResponse('Ok', 0, ['cities' => $cities]);

        return $cities;
    }

    protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null)
    {
        $this->packagesData->responseMessage = $responseMessage;

        $this->packagesData->responseCode = $responseCode;

        if ($responseData !== null && is_array($responseData)) {
            $this->packagesData->responseData = $responseData;
        } else {
            $this->packagesData->responseData = [];
        }
    }
}