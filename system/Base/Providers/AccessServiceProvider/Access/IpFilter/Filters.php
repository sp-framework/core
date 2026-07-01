<?php

namespace System\Base\Providers\AccessServiceProvider\Access\IpFilter;

use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFilters;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersDefault;

class Filters extends BasePackage
{
    protected $modelToUse = ServiceProviderAccessIpFilters::class;

    protected $ip;

    protected $ipFilterSettings;

    public $ip2location;

    public function init()
    {
        $this->app = $this->apps->getAppInfo();

        return $this;
    }

    public function setFilterSettings($ipFilterSettings)
    {
        $this->ipFilterSettings = $ipFilterSettings;
    }

    public function getFilters($data)
    {
        if (isset($data['app_id'])) {
            $this->app = $this->apps->apps[$data['app_id']];
        }

        $defaultStore = false;

        if (isset($data['defaultStore']) && $data['defaultStore'] == 'true') {
            $defaultStore = true;

            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $filters = $this->getAll();
        } else {
            $filters = [];

            if (isset($data['include_childrens'])) {
                $hosts = $this->getFilterByType('host', false, true);
            } else {
                $hosts = $this->getFilterByType('host');
            }

            if ($hosts && count($hosts) > 0) {
                $filters = array_merge($filters, $hosts);
            }

            $networks = $this->getFilterByType('network');
            if ($networks && count($networks) > 0) {
                $filters = array_merge($filters, $networks ?? []);
            }

            if (isset($data['include_childrens'])) {
                $ip2locationArr = $this->getFilterByType('ip2location');
            } else {
                $ip2locationArr = $this->getFilterByType('ip2location', false, true);
            }

            if ($ip2locationArr && count($ip2locationArr) > 0) {
                $ip2locationSortArr = [];

                foreach ($ip2locationArr as $ip2location) {
                    $ip2locationAddressArr = explode(':', $ip2location['address']);
                    if (count($ip2locationAddressArr) === 3) {
                        if (!isset($ip2locationSortArr[0])) {
                            $ip2locationSortArr[0] = [];
                        }
                        array_push($ip2locationSortArr[0], $ip2location);
                    } else if (count($ip2locationAddressArr) === 2) {
                        if (!isset($ip2locationSortArr[1])) {
                            $ip2locationSortArr[1] = [];
                        }
                        array_push($ip2locationSortArr[1], $ip2location);
                    } else if (count($ip2locationAddressArr) === 1) {
                        if (!isset($ip2locationSortArr[2])) {
                            $ip2locationSortArr[2] = [];
                        }
                        array_push($ip2locationSortArr[2], $ip2location);
                    }
                }

                if (count($ip2locationSortArr) > 0) {
                    ksort($ip2locationSortArr);

                    foreach (array_keys($ip2locationSortArr) as $ip2locationSortKey) {
                        $filters = array_merge($filters, $ip2locationSortArr[$ip2locationSortKey]);
                    }
                }
            }
        }

        if (count($filters) > 0) {
            if (!$defaultStore) {
                foreach ($filters as &$filter) {
                    if ($filter['address_type'] === 'host') {
                        $filter['ip_hits'] = '-';

                        continue;
                    }

                    if ($this->config->databasetype === 'db') {
                        $conditions =
                            [
                                'conditions'    => 'parent_id = :parent_id:',
                                'bind'          =>
                                    [
                                        'parent_id'   => $filter['id'],
                                    ]
                            ];
                    } else {
                        $conditions =
                            [
                                'conditions'    => [
                                    ['parent_id', '=', $filter['id']]
                                ]
                            ];
                    }

                    $childs = $this->getByParams($conditions);

                    $filter['ip_hits'] = 0;

                    if ($childs) {
                        $childs = count($childs);

                        if ($childs > 0) {
                            $filter['ip_hits'] = $childs;
                        }
                    }
                }
            }

            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filters' => $filters]);

            return $filters;
        } else if (count($filters) === 0) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('No Filters!', 0, ['filters' => $filters]);

            return $filters;
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('Error retrieving filters', 1);

        return false;
    }

    public function getFilterById($id, $getChildren = false, $defaultStore = false)
    {
        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $filter = $this->getById($id);
        } else {
            $filter = $this->getById($id);
        }

        if ($filter) {
            if ($filter['address_type'] !== 'host' &&
                $getChildren
            ) {
                if ($this->config->databasetype === 'db') {
                    $conditions =
                        [
                            'conditions'    => 'parent_id = :parent_id:',
                            'bind'          =>
                                [
                                    'parent_id'   => $filter['id'],
                                ]
                        ];
                } else {
                    $conditions =
                        [
                            'conditions'    => [
                                ['parent_id', '=', $filter['id']]
                            ]
                        ];
                }

                $filters = $this->getByParams($conditions);

                if ($filters && count($filters) > 0) {
                    $filter['ips'] = $filters;
                }
            }

            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['default_filter' => $defaultStore, 'filter' => $filter]);

            return $filter;
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filter found for the given id ' . $id, 1);

        return false;
    }

    public function getFilterByAddress($address, $getChildren = false, $defaultStore = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND address = :address:',
                    'bind'          =>
                        [
                            'app_id'    => $this->app['id'],
                            'address'   => $address,
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['address', '=', $address]
                    ]
                ];
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $getChildren = false;
        }

        $filter = $this->getByParams($conditions);

        if (isset($filter[0])) {
            if ($filter[0]['address_type'] !== 'host' &&
                $getChildren
            ) {
                if ($this->config->databasetype === 'db') {
                    $conditions =
                        [
                            'conditions'    => 'app_id = :app_id: AND parent_id = :parent_id:',
                            'bind'          =>
                                [
                                    'app_id'      => $this->app['id'],
                                    'parent_id'   => $filter[0]['id'],
                                ]
                        ];
                } else {
                    $conditions =
                        [
                            'conditions'    => [
                                ['app_id', '=', $this->app['id']],
                                ['parent_id', '=', $filter[0]['id']]
                            ]
                        ];
                }

                $filters = $this->getByParams($conditions);

                if ($filters && count($filters) > 0) {
                    $filter[0]['ips'] = $filters;
                }
            }

            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filter' => $filter[0]]);

            return $filter[0];
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filter found for the given address ' . $address, 1);

        return false;
    }

    public function getFilterByAddressAndType($address, $type, $defaultStore = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND address_type = :address_type: AND address = :address:',
                    'bind'          =>
                        [
                            'app_id'        => $this->app['id'],
                            'address_type'  => $type,
                            'address'       => $address,
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['address_type', '=', $type],
                        ['address', '=', $address]
                    ]
                ];
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        $filter = $this->getByParams($conditions);

        if (isset($filter[0])) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filter' => $filter[0]]);

            return $filter[0];
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filter found for the given address ' . $address, 1);

        return false;
    }

    public function getFilterByAddressTypeAndFilterType($addressType, $filterType, $defaultStore = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND address_type = :address_type: AND filter_type = :filter_type:',
                    'bind'          =>
                        [
                            'app_id'        => $this->app['id'],
                            'address_type'  => $addressType,
                            'filter_type'   => $filterType,
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['address_type', '=', $addressType],
                        ['filter_type', '=', $filterType]
                    ]
                ];
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        $filter = $this->getByParams($conditions);

        if ($filters) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filters' => $filters]);

            return $filters;
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filters found for the given address type and filter type', 1);

        return false;
    }

    public function getFilterByAddressType($addressType, $defaultStore = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND address_type = :address_type:',
                    'bind'          =>
                        [
                            'app_id'        => $this->app['id'],
                            'address_type'  => $addressType
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['address_type', '=', $addressType]
                    ]
                ];
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        $filter = $this->getByParams($conditions);

        if ($filters) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filters' => $filters]);

            return $filters;
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filters found for the given address type', 1);

        return false;
    }

    public function getFilterByType($type, $defaultStore = false, $children = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND address_type = :address_type:',
                    'bind'          =>
                        [
                            'app_id'        => $this->app['id'],
                            'address_type'  => $type,
                        ]
                ];

            if (!$children) {
                $conditions['conditions'] = 'app_id = :app_id: AND address_type = :address_type: AND parent_id = :parent_id:';
                $conditions['bind']['parent_id'] = NULL;
                $conditions['order'] = 'filter_type desc';
            }
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['address_type', '=', $type]
                    ],
                    'order'         => 'filter_type desc'
                ];

            if (!$children) {
                array_push($conditions['conditions'], ['parent_id', '=', null]);
            }
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        $filters = $this->getByParams($conditions);

        if ($filters) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filters' => $filters]);

            return $filters;
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filters found for the given type ' . $type, 1);

        return false;
    }

    public function getFilterByDecimal($decimal, $getChildren = false, $defaultStore = false)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'app_id = :app_id: AND decimal = :decimal:',
                    'bind'          =>
                        [
                            'app_id'   => $this->app['id'],
                            'decimal'   => $decimal,
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->app['id']],
                        ['decimal', '=', $decimal]
                    ]
                ];
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $getChildren = false;
        }

        $filter = $this->getByParams($conditions);

        if (isset($filter[0])) {
            if ($filter[0]['address_type'] !== 'host' &&
                $getChildren
            ) {
                if ($this->config->databasetype === 'db') {
                    $conditions =
                        [
                            'conditions'    => 'app_id = :app_id: AND parent_id = :parent_id:',
                            'bind'          =>
                                [
                                    'app_id'   => $this->app['id'],
                                    'parent_id'   => $filter[0]['id'],
                                ]
                        ];
                } else {
                    $conditions =
                        [
                            'conditions'    => [
                                ['app_id', '=', $this->app['id']],
                                ['parent_id', '=', $filter[0]['id']]
                            ]
                        ];
                }

                $filters = $this->getByParams($conditions);

                if ($filters && count($filters) > 0) {
                    $filter[0]['ips'] = $filters;
                }
            }

            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->addResponse('Ok', 0, ['filter' => $filter[0]]);

            return $filter[0];
        }

        $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->addResponse('No filter found for the given decimal ' . $decimal, 1);

        return false;
    }

    public function getFilterByParentId($id)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'parent_id = :parent_id:',
                    'bind'          =>
                        [
                            'parent_id'   => $id,
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['parent_id', '=', $id]
                    ]
                ];
        }

        $filters = $this->getByParams($conditions);

        if ($filters) {
            $this->addResponse('Ok', 0, ['filters' => $filters]);

            return $filters;
        }

        $this->addResponse('No filters found for the given parent ' . $id, 1);

        return false;
    }

    public function addFilter(array $data, $defaultStore = null)
    {
        if (!isset($data['filter_type']) ||
            (isset($data['filter_type']) &&
             ($data['filter_type'] !== 'allow' &&
              $data['filter_type'] !== 'block' &&
              $data['filter_type'] !== 'monitor')
            )
        ) {
            $this->addResponse('Please provide correct filter type', 1);

            return false;
        }

        if (!isset($data['address_type']) ||
            (isset($data['address_type']) &&
             ($data['address_type'] !== 'host' &&
              $data['address_type'] !== 'network' &&
              $data['address_type'] !== 'ip2location')
            )
        ) {
            $this->addResponse('Please provide correct address type', 1);

            return false;
        }

        if (!isset($data['address'])) {
            $this->addResponse('Please provide correct address', 1);

            return false;
        }

        if ($filterexists = $this->getFilterByAddress($data['address'])) {
            $this->addResponse('Filter with address ' . $data['address'] . ' already exists. Please see filter with ID: ' . $filterexists['id'], 1);

            return false;
        }

        if ($data['address_type'] === 'host' || $data['address_type'] === 'network') {
            if ($data['address_type'] === 'network') {
                if (!str_contains($data['address'], '/')) {
                    $this->addResponse('Please type correct network address. Format is CIDR - network address/network mask', 1);

                    return false;
                }

                if (str_contains($data['address'], ':')) {
                    $range = $this->ip2location->ipTools->cidrToIpv6($data['address']);
                } else {
                    $range = $this->ip2location->ipTools->cidrToIpv4($data['address']);
                }

                if (!isset($range['ip_start']) && !isset($range['ip_end'])) {
                    $this->addResponse('Please type correct network address. Format is CIDR - network address/network mask', 1);

                    return false;
                }
            } else if ($data['address_type'] === 'host') {
                if (str_contains($data['address'], '/')) {
                    $this->addResponse('Please type correct host address.', 1);

                    return false;
                }

                $this->ip = $data['address'];

                if (!$this->validateIP()) {
                    return false;
                }

                if ($this->ip2location->ipTools->isIpv4($this->ip)) {
                    $data['decimal'] = (int) $this->ip2location->ipTools->ipv4ToDecimal($this->ip);
                } else if ($this->ip2location->ipTools->isIpv6($this->ip)) {
                    $data['decimal'] = (int) $this->ip2location->ipTools->ipv6ToDecimal($this->ip);
                }
            }

            $data['ip2location_proxy'] = '-';
        } else if ($data['address_type'] === 'ip2location') {
            if ($this->ipFilterSettings['ip2location_primary_lookup_method'] === 'API' &&
                (!$this->ipFilterSettings['ip2location_io_api_key'] ||
                 $this->ipFilterSettings['ip2location_io_api_key'] === '')
            ) {
                $this->addResponse('Please set ip2location.io API key to get check address via Country, State & City', 1);

                return false;
            } else if ($this->ipFilterSettings['ip2location_primary_lookup_method'] === 'BIN') {
                $ip2locationInfo = $this->ip2location->getIp2locationInfo();

                if (!$ip2locationInfo ||
                    ($ip2locationInfo &&
                     (!isset($ip2locationInfo['bin_file_version']) ||
                      (isset($ip2locationInfo['bin_file_version']) && $ip2locationInfo['bin_file_version'] === '')
                     )
                    )
                ) {
                    $this->addResponse('Ip2location BIN file missing. Please re-download BIN file!', 1);

                    return false;
                }
            }

            if (!isset($data['ip2location_proxy']) ||
                (isset($data['ip2location_proxy']) &&
                 ($data['ip2location_proxy'] !== 'allow' &&
                  $data['ip2location_proxy'] !== 'block')
                )
            ) {
                if ($data['address_type'] === 'ip2location') {
                    $data['ip2location_proxy'] = 'allow';//Default is to allow proxy connections
                } else {
                    $data['ip2location_proxy'] = '-';//Default is to allow proxy connections
                }
            }

            $data['decimal'] = null;
        }

        if (!isset($data['updated_by'])) {
            $data['updated_by'] = 0;

            if (!$defaultStore && $this->access->auth->check()) {
                $data['updated_by'] = $this->access->auth->account()['id'];
            }
        }

        if (!isset($data['updated_at'])) {
            $data['updated_at'] = time();
        }

        if (!isset($data['parent_id'])) {
            $data['parent_id'] = null;
        }

        if (!isset($data['hit_count'])) {
            $data['hit_count'] = 0;
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $newFilter = $this->add($data);
        } else {
            if ($data['address_type'] === 'host') {
                $inDefaultFilter = $this->getFilterByDecimal($data['decimal'], false, true);

                if ($inDefaultFilter) {
                    $this->removeFilter(['id' => (int) $inDefaultFilter['id']], true);
                }
            }

            $newFilter = $this->add($data);
        }

        if ($newFilter) {
            $newFilter = $this->packagesData->last;

            if ($this->ipFilterSettings['log_filters'] === true) {
                $this->logger->logIpFilters->info($this->helper->encode(['action' => 'FILTER_ADD', 'filter' => $newFilter]));
            }

            $this->addResponse('Filter added');

            return $newFilter;
        }

        $this->addResponse('Not able to add filter', 1);

        return false;
    }

    public function removeFilter(array $data, $defaultStore = false)
    {
        if (!$filter = $this->getFilterById((int) $data['id'], false, $defaultStore)) {
            $this->addResponse('Filter with ID ' . $id . ' does not exists', 1);

            return false;
        }

        if (!$defaultStore) {
            $childFilters = $this->getFilterByParentId((int) $filter['id']);

            if ($childFilters && count($childFilters) > 0) {//Remove all childs
                foreach ($childFilters as $childFilter) {
                    $this->remove((int) $childFilter['id']);
                }
            }
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        $deleteFilter = $this->remove((int) $filter['id']);

        if ($deleteFilter) {
            if ($this->ipFilterSettings['log_filters'] === true) {
                $this->logger->logIpFilters->info($this->helper->encode(['action' => 'FILTER_DELETE', 'filter' => $filter]));
            }

            return $deleteFilter;
        }

        $this->addResponse('Unable to remove filter', 1);

        return false;
    }

    public function allowFilter(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide correct filter ID', 1);

            return false;
        }

        $filter = $this->getFirst('id', $data['id'], false, true, null, [], true);

        $filter['filter_type'] = 'allow';
        $filter['updated_by'] = $this->access->auth->account()['id'];
        $filter['updated_at'] = time();
        $filter['incorrect_login_attempts'] = null;
        $filter['hit_count'] = 0;

        if ($this->update($filter)) {
            $this->addResponse('Filter moved to allow.', 0);
        } else {
            $this->addResponse('Error allowing filter.', 1);
        }
    }

    public function blockFilter(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide correct filter ID', 1);

            return false;
        }

        $filter = $this->getFirst('id', $data['id'], false, true, null, [], true);

        if ($filter['address'] === $this->getVisitorIp()) {
            $this->addResponse('Cannot block your own IP!', 1);

            return false;
        }

        $filter['filter_type'] = 'block';
        $filter['updated_by'] = $this->access->auth->account()['id'];
        $filter['updated_at'] = time();
        $filter['incorrect_login_attempts'] = null;
        $filter['hit_count'] = 0;

        if ($this->update($filter)) {
            $this->addResponse('Filter moved to block.', 0);
        } else {
            $this->addResponse('Error blocking filter.', 1);
        }
    }

    public function checkIPFilter($filter, $ip = false, $defaultStore = false)
    {
        if ($ip) {//Check if IP is in default store and remove it
            $inDefaultFilter = $this->getFilterByAddress($ip, false, true);

            if ($inDefaultFilter) {
                $this->removeFilter(['id' => $inDefaultFilter['id']], true);
            }

            if ($filter['address_type'] === 'host') {
                $ip = false;
            }
        }

        if ($ip) {//Add a new Host Filter
            $parentFilter = $filter;

            $newFilter = $filter;


            $newFilter['address_type'] = 'host';
            $newFilter['address'] = $ip;
            if ($this->ip2location->ipTools->isIpv4($ip)) {
                $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv4ToDecimal($ip);
            } else if ($this->ip2location->ipTools->isIpv6($ip)) {
                $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv6ToDecimal($ip);
            }
            $newFilter['hit_count'] = 0;
            $newFilter['parent_id'] = $newFilter['id'];
            $newFilter['updated_by'] = 0;
            $newFilter['updated_at'] = time();
            unset($newFilter['id']);

            $filter = $this->addFilter($newFilter);
        }

        if (isset($filter['parent_id'])) {
            $parentFilter = $this->getFilterById($filter['parent_id']);
        }

        $this->bumpFilterHitCounter(false, null, $filter, $defaultStore);

        if ($filter['filter_type'] === 'allow' ||
            $filter['filter_type'] === 'monitor'
        ) {
            $status = 'Allowed';
            $code = 0;

            if ($filter['filter_type'] === 'monitor') {
                //AutoUnblock - only host ip can be auto unblocked.
                if ((int) $this->ipFilterSettings['auto_unblock_ip_minutes'] > 0) {
                    $blockedAt = Carbon::parse($filter['updated_at']);

                    if (time() > $blockedAt->addMinutes((int) $this->ipFilterSettings['auto_unblock_ip_minutes'])->timestamp) {
                        $this->removeFromMonitoring($filter);
                    } else {
                        $status = 'Monitoring';
                        $code = 2;
                    }
                } else {
                    $status = 'Monitoring';
                    $code = 2;
                }
            }

            if (isset($parentFilter)) {
                $this->bumpFilterHitCounter(false, null, $parentFilter, $defaultStore);

                $filter['parent_filter'] = $parentFilter;
            }

            if ($this->ipFilterSettings['debug_filters'] === true) {
                $this->logger->logIpFilters->debug($this->helper->encode(['status' => $status, 'ip' => $ip, 'filters_store'=> 'main', 'filter_id' => $filter['id']]));
            }

            $this->addResponse($status, $code, ['default_filter' => $defaultStore, 'filter' => $filter]);

            return true;
        }

        if ($this->ipFilterSettings['status'] === 'monitor') {
            if (isset($parentFilter)) {
                $this->bumpFilterHitCounter(false, null, $parentFilter, $defaultStore);

                $filter['parent_filter'] = $parentFilter;
            }

            if ($this->ipFilterSettings['debug_filters'] === true) {
                $this->logger->logIpFilters->debug(
                    $this->helper->encode(
                        ['status' => 'IP address is blocked, but firewall status is monitor so ip address is allowed!', 'default_filter' => $defaultStore, 'filter' => $filter]
                    )
                );
            }

            $this->addResponse('IP address is blocked, but firewall status is monitor so ip address is allowed!', 2, ['default_filter' => $defaultStore, 'filter' => $filter]);

            return true;
        }

        if (isset($parentFilter)) {
            $this->bumpFilterHitCounter(false, null, $parentFilter, $defaultStore);

            $filter['parent_filter'] = $parentFilter;
        }

        if ($this->ipFilterSettings['debug_filters'] === true) {
            $this->logger->logIpFilters->debug($this->helper->encode(['status' => 'BLOCKED', 'ip' => $ip, 'filters_store'=> 'main', 'filter_id' => $filter['id']]));
        }

        $this->addResponse('Blocked', 1, ['default_filter' => $defaultStore, 'filter' => $filter]);

        return false;
    }

    public function resetAppFilters(array $data)
    {
        if (!isset($data['app_id'])) {
            $this->addResponse('Incorrect App ID', 1);

            return;
        }

        if ($this->config->databasetype === 'db') {
            $app = $this->apps->getFirst('id', $data['app_id']);

            $filtersObj = $app->getIpFilters();

            if ($filtersObj && $filtersObj->count() > 0) {
                $filtersObj->delete();
            }

            $app->assign(['incorrect_login_attempt_block_ip' => 0, 'ip_filter_default_action' => 'allow']);

            $app->update();

            return true;
        } else {
            $this->apps->setFFRelations(true);

            $app = $this->apps->getFirst('id', (int) $data['app_id']);

            if ($app->data['ipFilters'] && count($app->data['ipFilters']) > 0) {
                foreach ($app->data['ipFilters'] as $filter) {
                    $this->removeFilter(['id' => $filter['id']]);
                }
            }

            $app = $app->toArray();

            $app['incorrect_login_attempt_block_ip'] = 0;
            $app['ip_filter_default_action'] = 'allow';

            $this->apps->update($app);

            return true;
        }

        $this->addResponse('Incorrect App ID', 1);

        return;
    }

    //This need fix.
    public function bumpFilterHitCounter($updateIncorrectAttempts = false, $appRoute = null, &$filter = null, $defaultStore = false)
    {
        if (!$this->ipFilterMiddlewareEnabled($appRoute)) {
            return;
        }

        if (!$filter) {
            $ip = $this->access->ipFilter->getVisitorIp();

            //Check in filter store
            $filter = $this->getFilterByAddressAndType($ip, 'host');

            if (!$filter) {
                //Check in default filter store
                $filter = $this->getFilterByAddressAndType($ip, 'host', true);

                $ipFilterSettings = $this->access->ipFilter->getIpFilterSettings();

                if (!$filter) {//Add new filter to default store
                    $newFilter =
                        [
                            'app_id'            => $this->app['id'],
                            'address'           => $ip,
                            'address_type'      => 'host',
                            'filter_type'       => $this->ipFilterSettings['default_filter'],
                            'updated_by'        => 0,
                            'updated_at'        => time()
                        ];

                    if ($this->ip2location->ipTools->isIpv4($ip)) {
                        $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv4ToDecimal($ip);
                    } else if ($this->ip2location->ipTools->isIpv6($ip)) {
                        $newFilter['decimal'] = (int) $this->ip2location->ipTools->ipv6ToDecimal($ip);
                    }

                    $defaultStore = true;

                    $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

                    $this->ffStore = $this->ff->store($this->ffStoreToUse);

                    $this->addFilter($newFilter);
                }
            }
        }

        $filter['hit_count'] = (int) $filter['hit_count'] + 1;

        if ($updateIncorrectAttempts) {
            if ($filter['incorrect_login_attempts'] !== null) {
                $filter['incorrect_login_attempts'] = (int) $filter['incorrect_login_attempts'] + 1;
            } else {
                $filter['incorrect_login_attempts'] = 1;
            }

            if ((int) $this->ipFilterSettings['incorrect_login_attempt_block_ip'] !== 0 &&
                $filter['incorrect_login_attempts'] >= (int) $this->ipFilterSettings['incorrect_login_attempt_block_ip']
            ) {
                $filter['filter_type'] = 'block';
            } else {
                $filter['filter_type'] = 'monitor';
            }
        }

        if ($defaultStore) {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFiltersDefault::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        } else {
            $this->setModelToUse($this->modelToUse = ServiceProviderAccessIpFilters::class);

            $this->ffStore = $this->ff->store($this->ffStoreToUse);
        }

        if ($this->update($filter)) {
            return true;
        }

        return false;
    }

    //This need work
    public function removeFromMonitoring()
    {
        if (!$this->ipFilterMiddlewareEnabled()) {
            return;
        }

        $this->apps->setFFRelations(true);

        $app = $this->apps->getFirst('id', $this->app['id']);

        $filterStore = $this->ff->store($this->useModel()->getSource());

        if ($this->config->databasetype === 'db') {
            $filter = $app->getMonitorlist();

            if ($filter && $filter->count() > 0) {
                $filter->delete();
            }
        } else {
            $filter = $filterStore->findOneBy([['ip_address', '=', $this->getDi()->getRequest()->getClientAddress()], ['added_by', '=', 0]]);

            if ($filter && count($filter) > 0) {
                $filterStore->deleteById($filter['id']);
            }
        }
    }

    protected function ipFilterMiddlewareEnabled($appRoute = null)
    {
        $middleware = $this->modules->middlewares->getMiddlewareByNameForAppId('IpFilter', $this->apps->getAppInfo($appRoute)['id']);

        if (isset($middleware['apps'][$this->app['id']]['enabled']) &&
            $middleware['apps'][$this->app['id']]['enabled'] === true
        ) {
            return true;
        }

        return false;
    }

    public function validateIP($ip = null)
    {
        if (!$this->ip && $ip) {
            $this->ip = $ip;
        }

        $ipv6 = false;
        if ($this->ip2location->ipTools->isIpv6($this->ip)) {
            $ipv6 = true;
        }

        if (!$ipv6 && !$this->ipFilterSettings['filter_ipv4']) {
            if ($this->ipFilterSettings['debug_filters'] === true) {
                $this->logger->logIpFilters->debug(
                    $this->helper->encode(
                        [
                            'action' => 'IpFilter blocked connection as filter_ipv4 settings is set to false and ip address is from ipv4 range.',
                            'ip' => $this->ip
                        ]
                    )
                );
            }

            $this->addResponse('Please enter correct ip address', 1);

            return false;
        }

        if ($ipv6 && !$this->ipFilterSettings['filter_ipv6']) {
            if ($this->ipFilterSettings['debug_filters'] === true) {
                $this->logger->logIpFilters->debug(
                    $this->helper->encode(
                        [
                            'action' => 'IpFilter blocked connection as filter_ipv6 settings is set to false and ip address is from ipv6 range.',
                            'ip' => $this->ip
                        ]
                    )
                );
            }

            $this->addResponse('Please enter correct ip address', 1);

            return false;
        }

        if ($ipv6) {
            if (!filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                if ($this->ipFilterSettings['debug_filters'] === true) {
                    $this->logger->logIpFilters->debug(
                        $this->helper->encode(
                            [
                                'action' => 'Please enter correct ipv6 address',
                                'ip' => $this->ip
                            ]
                        )
                    );
                }

                $this->addResponse('Please enter correct ipv6 address', 1);

                return false;
            }
        } else {
            if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
                if ($this->ipFilterSettings['debug_filters'] === true) {
                    $this->logger->logIpFilters->debug(
                        $this->helper->encode(
                            [
                                'action' => 'Please enter correct ipv4 address',
                                'ip' => $this->ip
                            ]
                        )
                    );
                }

                $this->addResponse('Please enter correct ipv4 address', 1);

                return false;
            }
        }

        $allow_private_range = true;
        if (array_key_exists('allow_private_range', $this->ipFilterSettings) &&
            !is_null($this->ipFilterSettings['allow_private_range']) &&
            $this->ipFilterSettings['allow_private_range'] === false
        ) {
            $allow_private_range = false;
        }

        if (!$allow_private_range) {
            if (!filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                if ($this->ipFilterSettings['debug_filters'] === true) {
                    $this->logger->logIpFilters->debug(
                        $this->helper->encode(
                            [
                                'action' => 'IpFilter blocked connection as allow_private_range settings is set to false and ip address is from private range.',
                                'ip' => $this->ip
                            ]
                        )
                    );
                }

                $this->addResponse('IpFilter blocked connection as allow_private_range settings is set to false and ip address is from private range.', 1);

                return false;
            }
        }

        $allow_reserved_range = true;
        if (array_key_exists('allow_reserved_range', $this->ipFilterSettings) &&
            !is_null($this->ipFilterSettings['allow_reserved_range']) &&
            $this->ipFilterSettings['allow_reserved_range'] === false
        ) {
            $allow_reserved_range = false;
        }

        if (!$allow_reserved_range) {
            if (!filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
                if ($this->ipFilterSettings['debug_filters'] === true) {
                    $this->logger->logIpFilters->debug(
                        $this->helper->encode(
                            [
                                'action' => 'IpFilter blocked connection as allow_reserved_range settings is set to false and ip address is from reserved range.',
                                'ip' => $this->ip
                            ]
                        )
                    );
                }

                $this->addResponse('IpFilter blocked connection as allow_reserved_range settings is set to false and ip address is from reserved range.', 1);

                return false;
            }
        }

        return true;
    }
}