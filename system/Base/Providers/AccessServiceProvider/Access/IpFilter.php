<?php

namespace System\Base\Providers\AccessServiceProvider\Access;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\IpUtils;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Access\IpFilter\Filters;
use System\Base\Providers\AccessServiceProvider\Access\IpFilter\Ip2location;
use System\Base\Providers\AppsServiceProvider\Exceptions\IpFilterBlockedException;

class IpFilter extends BasePackage
{
    protected $packageName = 'ipfilter';

    public $ip;

    public $filters;

    public $ip2location;

    protected $ipFilterSettings;

    protected $app;

    public function init()
    {
        $this->app = $this->apps->getAppInfo();

        $this->filters = (new Filters())->init();

        $this->ip2location = new Ip2location($this);

        $this->getIpFilterSettings();

        $this->filters->setFilterSettings($this->ipFilterSettings);

        $this->filters->ip2location = $this->ip2location;

        return $this;
    }

    public function getIpFilterSettings()
    {
        if (!$this->ipFilterSettings) {
            $ipfilterMiddleware = $this->modules->middlewares->getMiddlewareByNameForAppId('IpFilter', $this->app['id']);

            if ($ipfilterMiddleware) {
                if (isset($ipfilterMiddleware['apps'][$this->app['id']]['settings'])) {
                    $this->ipFilterSettings = $ipfilterMiddleware['apps'][$this->app['id']]['settings'];
                } else if (isset($ipfilterMiddleware['settings'])) {//Load default settings
                    $this->ipFilterSettings = $ipfilterMiddleware['settings'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['ipFilterSettings' => $this->ipFilterSettings]);

        return $this->ipFilterSettings;
    }

    public function setVisitorIp($ip = null)
    {
        if (!$ip) {
            $this->ip = $this->ip2location->ipTools->getVisitorIp();
        } else {
            $this->ip = $ip;
        }
    }

    public function getVisitorIp($ip = null)
    {
        if (!$this->ip) {
            $this->setVisitorIp($ip);
        }

        return $this->ip;
    }

    public function checkIp($ip = null, array $overrideIp2locationLookupSequence = null, $checkViaApp = false)
    {
        $this->ip = $ip;

        if (!$this->ip) {
            $this->getVisitorIp();
        }

        if ($this->ip === "127.0.0.1") {
            return true;
        }

        //Zero Check - We check OpCache
        $profiling = [];
        $this->basepackages->utils->setMicroTimer('Cached Filters', true, true);

        $opCacheFilters = [];
        $cached = false;
        if ($this->opCache && $this->opCache->checkCache($this->app['route'], 'filters')) {
            $opCacheFilters = $this->opCache->getCache($this->app['route'], 'filters');

            if (isset($opCacheFilters[$this->ip])) {
                $this->basepackages->utils->setMicroTimer('Cached Filters', true);

                $profiling = $this->basepackages->utils->getMicroTimer();

                if ($checkViaApp) {
                    $cached = true;
                } else {
                    return $opCacheFilters[$this->ip];
                }
            }
        }

        if (!$this->filters->validateIP($this->ip)) {
            $this->addResponse(
                $this->filters->packagesData->responseMessage,
                $this->filters->packagesData->responseCode,
                $this->filters->packagesData->responseData ?? []
            );

            return false;
        }

        //First Check - We check HOST entries
        if (!$cached) {
            $profiling = [];
            $this->basepackages->utils->setMicroTimer('Host Filters', true, true);
        }

        $filter = $this->filters->getFilterByAddressAndType($this->ip, 'host');

        if ($filter) {//We find the address in address_type host
            $hostCheckIpFilter = $this->filters->checkIPFilter($filter, $this->ip);

            if (count($profiling) === 0) {
                $this->basepackages->utils->setMicroTimer('Host Filters', true);

                $profiling = $this->basepackages->utils->getMicroTimer();
            }

            $responseData = $this->filters->packagesData->responseData ?? [];

            if (count($responseData) > 0) {
                $responseData['profiling'] = $profiling;
                $responseData['cached'] = $cached;
            }

            $this->addResponse(
                $this->filters->packagesData->responseMessage,
                $this->filters->packagesData->responseCode,
                $responseData
            );

            if ($this->opCache) {
                $opCacheFilters[$this->ip] = false;

                if ($filter['filter_type'] === 'allow') {
                    $opCacheFilters[$this->ip] = true;
                }

                $this->opCache->setCache($this->app['route'], $opCacheFilters, 'filters');
            }

            return $hostCheckIpFilter;
        }

        //Second Check - We check NETWORK entries
        if (!$cached) {
            $profiling = [];
            $this->basepackages->utils->setMicroTimer('Network Filters', true, true);
        }

        $filters = $this->filters->getFilterByType('network');

        if ($filters && count($filters) > 0) {
            foreach ($filters as $filterKey => $filter) {
                if (IpUtils::checkIp($this->ip, $filter['address'])) {
                    $networkCheckIpFilter = $this->filters->checkIPFilter($filter, $ip);

                    if (count($profiling) === 0) {
                        $this->basepackages->utils->setMicroTimer('Network Filters', true);

                        $profiling = $this->basepackages->utils->getMicroTimer();
                    }

                    $responseData = $this->filters->packagesData->responseData ?? [];

                    if (count($responseData) > 0) {
                        if (isset($responseData['filter'])) {
                            if ($this->opCache) {
                                $opCacheFilters[$this->ip] = false;

                                if ($responseData['filter']['filter_type'] === 'allow') {
                                    $opCacheFilters[$this->ip] = true;
                                }

                                $this->opCache->setCache($this->app['route'], $opCacheFilters, 'filters');
                            }
                        }

                        $responseData['profiling'] = $profiling;
                        $responseData['cached'] = $cached;
                    }

                    $this->addResponse(
                        $this->filters->packagesData->responseMessage,
                        $this->filters->packagesData->responseCode,
                        $this->filters->packagesData->responseData ?? []
                    );

                    return $networkCheckIpFilter;
                }
            }
        }

        //Third Check - We check ip2location as per the primary set first and then secondary if we did not find the entry
        if ($this->ip2location->checkIPIsPublic($this->ip)) {
            if (!$cached) {
                $profiling = [];
                $this->basepackages->utils->setMicroTimer('Ip2location Filters', true, true);
            }

            $filters = $this->filters->getFilterByType('ip2location');

            if ($filters && count($filters) > 0) {
                $ip2locationFilters = [];

                foreach ($filters as $filterKey => $filter) {
                    $ip2locationAddressArr = explode(':', $filter['address']);

                    if (count($ip2locationAddressArr) === 1) {
                        $ip2locationFilters[strtolower($ip2locationAddressArr[0])]['id'] = $filter['id'];
                    } else if (count($ip2locationAddressArr) === 2) {
                        $ip2locationFilters[strtolower($ip2locationAddressArr[0])][strtolower($ip2locationAddressArr[1])]['id'] = $filter['id'];
                    } else if (count($ip2locationAddressArr) === 3) {
                        $ip2locationFilters[strtolower($ip2locationAddressArr[0])][strtolower($ip2locationAddressArr[1])][strtolower($ip2locationAddressArr[2])]['id'] = $filter['id'];
                    }
                }

                if (count($ip2locationFilters) > 0) {
                    $ip2locationLookupOptions = ['API', 'BIN'];
                    $overrideIp2locationLookupSequence = ['API', 'BIN'];//remove this!

                    if ($overrideIp2locationLookupSequence && count($overrideIp2locationLookupSequence) === 2) {
                        $ip2locationLookupOptions = $overrideIp2locationLookupSequence;
                    }

                    if (in_array($this->ipFilterSettings['ip2location_primary_lookup_method'], $ip2locationLookupOptions)) {
                        if (!$overrideIp2locationLookupSequence) {
                            $arrayKey = array_keys($ip2locationLookupOptions, $this->ipFilterSettings['ip2location_primary_lookup_method']);

                            $ip2locationLookupOptionsMethod = strtoupper($ip2locationLookupOptions[$arrayKey[0]]);
                        } else {
                            $ip2locationLookupOptionsMethod = strtoupper($ip2locationLookupOptions[0]);
                        }

                        $lookupMethod = 'getIpDetailsFromIp2location' . $ip2locationLookupOptionsMethod;

                        $response = $this->ip2location->$lookupMethod($ip);

                        if (!$response) {//Not found in primary lookup, we get the secondary from list.
                            unset($ip2locationLookupOptions[$arrayKey[0]]);

                            $ip2locationLookupOptions = array_values($ip2locationLookupOptions);

                            $ip2locationLookupOptionsMethod = strtoupper($ip2locationLookupOptions[0]);

                            $lookupMethod = 'getIpDetailsFromIp2location' . $ip2locationLookupOptionsMethod;

                            $response = $this->ip2location->$lookupMethod($ip);
                        }

                        if ($response) {
                            $filterRule = null;

                            if (isset($ip2locationFilters[strtolower($response['country_code'])][strtolower($response['region_name'])][strtolower($response['city_name'])]['id'])) {
                                $filterRule = $ip2locationFilters[strtolower($response['country_code'])][strtolower($response['region_name'])][strtolower($response['city_name'])]['id'];
                            } else if (isset($ip2locationFilters[strtolower($response['country_code'])][strtolower($response['region_name'])]['id'])) {
                                $filterRule = $ip2locationFilters[strtolower($response['country_code'])][strtolower($response['region_name'])]['id'];
                            } else if (isset($ip2locationFilters[strtolower($response['country_code'])]['id'])) {
                                $filterRule = $ip2locationFilters[strtolower($response['country_code'])]['id'];
                            }

                            if ($filterRule) {
                                $filter = $this->filters->getFilterById($filterRule);

                                if (isset($filter['ip2location_proxy']) && $filter['ip2location_proxy'] === 'block') {
                                    if (isset($response['is_proxy']) && $response['is_proxy'] === true) {
                                        $filter['filter_type'] = 'block';
                                    }
                                }

                                $ip2locationCheckIpFilter = $this->filters->checkIPFilter($filter, $ip);

                                if (count($profiling) === 0) {
                                    $this->basepackages->utils->setMicroTimer('Ip2location Filters', true);

                                    $profiling = $this->basepackages->utils->getMicroTimer();
                                }

                                $responseData = $this->filters->packagesData->responseData ?? [];

                                if (count($responseData) > 0) {
                                    if (isset($responseData['filter'])) {
                                        if ($this->opCache) {
                                            $opCacheFilters[$this->ip] = false;

                                            if ($responseData['filter']['filter_type'] === 'allow') {
                                                $opCacheFilters[$this->ip] = true;
                                            }

                                            $this->opCache->setCache($this->app['route'], $opCacheFilters, 'filters');
                                        }
                                    }

                                    $responseData['profiling'] = $profiling;
                                    $responseData['cached'] = $cached;
                                }

                                $this->addResponse(
                                    $this->filters->packagesData->responseMessage,
                                    $this->filters->packagesData->responseCode,
                                    $responseData
                                );

                                return $ip2locationCheckIpFilter;
                            }
                        }
                    }
                }
            }
        }

        //Forth - We check DEFAULT entries in default store
        if (!$cached) {
            $profiling = [];
            $this->basepackages->utils->setMicroTimer('Default Filters', true, true);
        }

        $filter = $this->filters->getFilterByAddressAndType($this->ip, 'host', true);

        if ($filter) {//We find the address in default store and bump its counter
            $this->filters->bumpFilterHitCounter(false, null, $filter, true);
        } else {//We add a new entry in default store
            $newFilter['app_id'] = $this->app['id'];
            $newFilter['address_type'] = 'host';
            $newFilter['address'] = $this->ip;
            if ($this->ip2location->ipTools->isIpv4($this->ip)) {
                $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv4ToDecimal($this->ip);
            } else if ($this->ip2location->ipTools->isIpv6($this->ip)) {
                $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv6ToDecimal($this->ip);
            }
            $newFilter['hit_count'] = 1;
            $newFilter['updated_by'] = 0;
            $newFilter['updated_at'] = time();
            $newFilter['filter_type'] = $this->ipFilterSettings['default_filter'];

            $filter = $this->filters->addFilter($newFilter, true);
        }

        if (count($profiling) === 0) {
            $this->basepackages->utils->setMicroTimer('Default Filters', true);

            $profiling = $this->basepackages->utils->getMicroTimer();
        }

        $responseData = [];
        $responseData['default_filter'] = true;
        $responseData['filter'] = $filter;

        if (count($responseData) > 0) {
            if (isset($responseData['filter'])) {
                if ($this->opCache) {
                    $opCacheFilters[$this->ip] = false;

                    if ($responseData['filter']['filter_type'] === 'allow') {
                        $opCacheFilters[$this->ip] = true;
                    }

                    $this->opCache->setCache($this->app['route'], $opCacheFilters, 'filters');
                }
            }

            $responseData['profiling'] = $profiling;
            $responseData['cached'] = $cached;
        }

        if ($this->ipFilterSettings['default_filter'] === 'allow') {
            $this->addResponse('Allowed', 0, $responseData);

            if ($this->ipFilterSettings['log_filters'] === true) {
                $this->logger->logIpFilters->info($this->helper->encode(['action' => 'ALLOWED', 'ip' => $this->ip, 'filters_store'=> 'default', 'filter_id' => $filter['id']]));
            }

            return true;
        } else if ($this->ipFilterSettings['default_filter'] === 'block') {
            if ($this->ipFilterSettings['status'] === 'monitor') {
                $this->addResponse('IP address is blocked, but firewall status is monitor so ip address is allowed!', 2, $responseData);

                return true;
            }

            if ($this->ipFilterSettings['log_filters'] === true) {
                $this->logger->logIpFilters->info($this->helper->encode(['action' => 'BLOCKED', 'ip' => $this->ip, 'filters_store'=> 'default', 'filter_id' => $filter['id']]));
            }

            $this->addResponse('Blocked', 1, $responseData);

            return false;
        }

        return true;
    }
}