<?php

namespace System\Base\Providers\AccessServiceProvider\Access\IpFilter;

use IP2LocationIO\Configuration;
use IP2LocationIO\IPGeolocation;
use IP2Location\Database;
use IP2Location\IpTools;

class Ip2location
{
    public $ipTools;

    public $ipGeoLocation;

    public $ipFilterFiltersIp2locationStore;

    protected $dataPath = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Ip2location';

    protected $ipFilter;

    protected $ipFilterSettings;

    public function __construct($ipFilter)
    {
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
                    $this->dataPath . '/' . $this->ipFilterSettings['ip2location_bin_file_code'] . '.BIN',
                    constant('\IP2Location\Database::' . $this->ipFilterSettings['ip2location_bin_access_mode'])
                );
        } catch (\throwable $e) {
            //Log here
            if (str_contains($e->getMessage(), 'exist')) {
                $this->ipFilter->addResponse('Bin file does not exist, please download bin file first to check in bin file.', 1);
            } else {
                $this->ipFilter->addResponse($e->getMessage(), 1);
            }

            return false;
        }

        $ipDetailsArr = $ip2locationBin->lookup($ip, \IP2Location\Database::ALL);

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

            $this->ipFilter->addResponse('Details for IP: ' . $ip . ' retrieved successfully using BIN file.', 0, ['ip_details' => $ipDetails]);

            return $ipDetails;
        }

        $this->ipFilter->addResponse('Details for IP: ' . $ip . ' not available in the BIN file. Please search API.', 2);

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
                    $this->dataPath . '/' . $this->ipFilterSettings['ip2location_proxy_bin_file_code'] . '.BIN',
                    constant('\IP2Proxy\Database::' . $this->ipFilterSettings['ip2location_proxy_bin_access_mode'])
                );

            $ipDetailsArr = $ip2locationProxyBin->lookup($ip, \IP2Proxy\Database::ALL);
        } catch (\throwable $e) {
            //Log here
            if (str_contains($e->getMessage(), 'exist')) {
                $this->ipFilter->addResponse('Bin file does not exist, please download bin file first to check in bin file.', 1);
            } else {
                $this->ipFilter->addResponse($e->getMessage(), 1);
            }

            return false;
        }

        if ($ipDetailsArr && isset($ipDetailsArr['countryCode']) && $ipDetailsArr['countryCode'] !== '-') {
            $ipDetails['address'] = $ip;
            $ipDetails['is_proxy'] = $ipDetailsArr['isProxy'];
            $ipDetails['proxy_type'] = $ipDetailsArr['proxyType'];

            $this->ipFilter->addResponse('Details for IP: ' . $ip . ' retrieved successfully using Proxy BIN file.', 0, ['ip_details' => $ipDetails]);

            return $ipDetails;
        }

        $this->ipFilter->addResponse('Details for IP: ' . $ip . ' not available in the Proxy BIN file. Please search API.', 2);

        return false;
    }

    public function getIpDetailsFromIp2locationAPI($ip)
    {
        if (!$this->checkIPIsPublic($ip)) {
            return false;
        }

        $index = $this->ipFilter->indexes->searchIndexes($ip, true);

        if ($index) {
            $ipFilterFiltersIp2locationStoreEntry = $this->ipFilterFiltersIp2locationStore->findById((int) $index);

            if ($ipFilterFiltersIp2locationStoreEntry) {
                $this->ipFilter->addResponse('Details for IP: ' . $ip . ' retrieved successfully using indexes.', 0, ['ip_details' => $ipFilterFiltersIp2locationStoreEntry]);

                return $ipFilterFiltersIp2locationStoreEntry;
            }
        }

        $ipFilterFiltersIp2locationStoreEntry = $this->ipFilterFiltersIp2locationStore->findBy(['address', '=', $ip]);

        if ($ipFilterFiltersIp2locationStoreEntry && isset($ipFilterFiltersIp2locationStoreEntry[0])) {
            $this->ipFilter->addResponse('Details for IP: ' . $ip . ' retrieved successfully using ip2location local database.', 0, ['ip_details' => $ipFilterFiltersIp2locationStoreEntry[0]]);

            $this->ipFilter->indexes->addToIndex($ipFilterFiltersIp2locationStoreEntry[0], false, true);//Add to index

            return $ipFilterFiltersIp2locationStoreEntry[0];
        }

        if ($this->ipGeoLocation) {
            try {
                $apiCallResponse = $this->ipGeoLocation->lookup($ip, $this->ipFilterSettings['ip2location_io_api_language']);

                if ($apiCallResponse) {
                    $apiCallResponse = json_decode(json_encode($apiCallResponse), true);

                    $ipDetails['address'] = $apiCallResponse['ip'];
                    $ipDetails['country_code'] = $apiCallResponse['country_code'];
                    $ipDetails['region_name'] = $apiCallResponse['region_name'];
                    $ipDetails['city_name'] = $apiCallResponse['city_name'];
                    $ipDetails['is_proxy'] = $apiCallResponse['is_proxy'];
                    $ipDetails['proxy_type'] = '-';
                    if (isset($apiCallResponse['proxy']) && isset($apiCallResponse['proxy']['proxy_type'])) {
                        $ipDetails['proxy_type'] = $apiCallResponse['proxy']['proxy_type'];
                    }

                    $ipDetails = $this->ipFilterFiltersIp2locationStore->insert($ipDetails);

                    $this->ipFilter->indexes->addToIndex($ipDetails, false, true);//Add to index

                    $this->ipFilter->addResponse('Details for IP: ' . $ip . ' retrieved successfully using API.', 0, ['ip_details' => $ipDetails]);

                    return $apiCallResponse;
                }
            } catch (\throwable $e) {
                //Log here
                $this->ipFilter->systemLogger->error('ERROR_IP2LOCATION', [$e->getMessage()]);
                $this->ipFilter->addResponse($e->getMessage(), 1);
            }
        } else {
            $this->ipFilter->addResponse('Lookup is using io API and io API keys are not set!', 1);
        }

        return false;
    }

    protected function checkIPIsPublic($ip)
    {
        if ($this->ipFilter->validateIP($ip)) {
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
                $this->ipFilter->addResponse('IP Address : ' . $ip . ' is from a private range of IP addresses!', 2);

                return false;
            }
        } else {
            return false;
        }

        return true;
    }
}