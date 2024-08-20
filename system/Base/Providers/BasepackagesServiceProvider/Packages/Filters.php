<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

// use Apps\Core\Packages\Hrms\Employees\Employees;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesFilters;

class Filters extends BasePackage
{
    protected $modelToUse = BasepackagesFilters::class;

    protected $packageName = 'filters';

    public $filters;

    public function getFiltersForComponent(int $componentId)
    {
        $checkShowAllFilters = $this->checkShowAllFilters($componentId);

        if ($checkShowAllFilters) {
            return $this->getFilters($componentId);

        } else {
            $this->addShowAllFilter($componentId);

            return $this->getFilters($componentId);
        }
    }

    public function getFiltersForAccountAndComponent(array $account, int $componentId)
    {
        $checkShowAllFilters = $this->checkShowAllFilters($componentId);

        if ($checkShowAllFilters) {
            return $this->getFilters($componentId, $account);

        } else {
            $this->addShowAllFilter($componentId);

            return $this->getFilters($componentId, $account);
        }
    }

    protected function checkShowAllFilters(int $componentId)
    {
        if ($this->config->databasetype === 'db') {
            return
                $this->getByParams(
                    [
                        'conditions'    => 'component_id = :cid: AND auto_generated = :ag:',
                        'bind'          => [
                            'cid'       => $componentId,
                            'ag'        => 1
                        ]
                    ], true
                );
        } else {
            return $this->getByParams(['conditions' => [['component_id', '=', $componentId], ['auto_generated', '=', 1]]]);
        }
    }

    protected function getFilters(int $componentId, array $account = null)
    {
        $component = $this->modules->components->getComponentById($componentId);

        // $employeesPackage = $this->init()->checkPackage('Apps\Core\Packages\Hrms\Employees\Employees');

        // if ($employeesPackage) {
        //     $employeesPackage = new \Apps\Core\Packages\Hrms\Employees\Employees;
        // }

        if ($account && isset($account['id'])) {
            if ($this->config->databasetype === 'db') {
                $filtersArr =
                    $this->getByParams(
                        [
                            'conditions'    => 'component_id = :cid: AND (account_id = :aid: OR account_id = :aid0:)',
                            'bind'          => [
                                'cid'       => $componentId,
                                'aid'       => $account['id'],
                                'aid0'      => 0
                            ]
                        ], true
                    );
            } else {
                $filtersArr =
                    $this->getByParams(
                        [
                            'conditions'    =>
                                [
                                    ['component_id', '=', $componentId],
                                ],
                                [
                                    ['account_id', '=', $account['id']],
                                    'OR',
                                    ['account_id', '=', 0],
                                ]
                        ]
                    );
            }

            $myFilters = [];

            //Make System Filters above all
            if ($filtersArr) {
                $filtersArr = msort($filtersArr, 'filter_type');

                foreach ($filtersArr as $filterKey => $filter) {
                    $filter['shared'] = '0';

                    $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];

                    if ($filter['filter_type'] == 1 &&
                        $filter['account_id'] == $account['id']
                    ) {
                        array_push($myFilters, $filter['id']);

                        if ($filter['shared_ids']) {//Im Sharing

                            $filter['shared_ids'] = $this->helper->decode($filter['shared_ids'], true);

                            //Role Ids
                            if (isset($filter['shared_ids']['rids']) &&
                                count($filter['shared_ids']['rids']) > 0
                            ) {
                                foreach ($filter['shared_ids']['rids'] as $sharingRidKey => $sharingRid) {
                                    $role = $this->roles->getById($sharingRid);
                                    if ($role) {
                                        $filter['shared_ids']['rids'][$sharingRidKey] =
                                        [
                                            'id'    => $sharingRid,
                                            'name'  => $role['name']
                                        ];
                                    }
                                }
                            }

                            //Account Ids
                            if (isset($filter['shared_ids']['aids']) &&
                                count($filter['shared_ids']['aids']) > 0
                            ) {
                                foreach ($filter['shared_ids']['aids'] as $sharingAidKey => $sharingAid) {
                                    $sharingAccount = $this->accounts->getById($sharingAid);

                                    if ($sharingAccount) {
                                        $filter['shared_ids']['aids'][$sharingAidKey] =
                                        [
                                            'id'    => $sharingAid,
                                            'name'  => $sharingAccount['email']
                                        ];
                                    }

                                    // if ($employeesPackage) {
                                    //     $employee = $employeesPackage->searchByAccountId($sharingAid);

                                    //     if ($employee) {
                                    //         $filter['shared_ids']['eids'][$sharingAidKey] =
                                    //             [
                                    //                 'id'    => $sharingAid,
                                    //                 'name'  => $employee['full_name']
                                    //             ];
                                    //     }
                                    // }
                                }
                            }

                            $filter['shared_ids'] = $this->escaper->escapeHtml($this->helper->encode($filter['shared_ids']));
                        }
                    }
                    $filters[$filter['id']] = $filter;
                }
            }

            if ($this->config->databasetype === 'db') {
                $sharedFiltersArr =
                    $this->getByParams(
                        [
                            'conditions'    => 'component_id = :cid: AND shared_ids IS NOT NULL',
                            'bind'          => [
                                'cid'       => $componentId
                            ]
                        ]
                    );
            } else {
                $sharedFiltersArr =
                    $this->getByParams(
                        [
                            'conditions'    => [
                                ['component_id', '=', $componentId],
                                ['shared_ids', '!=', null]
                            ]
                        ]
                    );
            }

            if ($sharedFiltersArr) {//Shared By Others
                foreach ($sharedFiltersArr as $filterKey => $filter) {
                    $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];

                    if (!in_array($filter['id'], $myFilters)) {

                        $filter['shared_ids'] = $this->helper->decode($filter['shared_ids'], true);

                        if (isset($filter['shared_ids']['rids']) &&
                            count($filter['shared_ids']['rids']) > 0
                        ) {
                            foreach ($filter['shared_ids']['rids'] as $ridKey => $rid) {
                                if ($rid == $account['role_id']) {

                                    // if ($employeesPackage) {
                                    //     $employee = $employeesPackage->searchByAccountId($filter['account_id']);

                                    //     if ($employee) {
                                    //         $filter['employee_full_name'] = $employee['full_name'];
                                    //     }
                                    // } else {
                                        $sharedBy = $this->accounts->getById($filter['account_id']);

                                        if ($sharedBy) {
                                            $filter['account_email'] = $sharedBy['email'];
                                        }
                                    // }

                                    $filter['shared_ids'] = $this->escaper->escapeHtml($this->helper->encode($filter['shared_ids']));

                                    $filter['shared'] = '1';

                                    $sharedFilters[$filter['id']] = $filter;
                                }
                            }
                        }
                        if (isset($filter['shared_ids']['aids']) &&
                            count($filter['shared_ids']['aids']) > 0
                        ) {
                            foreach ($filter['shared_ids']['aids'] as $aidKey => $aid) {
                                if ($aid == $account['id']) {

                                    // if ($employeesPackage) {
                                    //     $employee = $employeesPackage->searchByAccountId($filter['account_id']);

                                    //     if ($employee) {
                                    //         $filter['employee_full_name'] = $employee['full_name'];
                                    //     }
                                    // } else {
                                        $sharedBy = $this->accounts->getById($filter['account_id']);

                                        if ($sharedBy) {
                                            $filter['account_email'] = $sharedBy['email'];
                                        }
                                    // }

                                    $filter['shared_ids'] = $this->escaper->escapeHtml($this->helper->encode($filter['shared_ids']));

                                    $filter['shared'] = '1';

                                    $sharedFilters[$filter['id']] = $filter;
                                }
                            }
                        }
                    }
                }

                if (isset($sharedFilters)) {
                    return array_merge($filters, $sharedFilters);
                }
            }

            return $filters;
        }

        if ($this->config->databasetype === 'db') {
            $filtersArr =
                $this->getByParams(
                    [
                        'conditions'    => 'component_id = :cid:',
                        'bind'          => [
                            'cid'       => $componentId
                        ]
                    ]
                );
        } else {
            $filtersArr =
                $this->getByParams(
                    [
                        'conditions'    => [
                            ['component_id', '=', $componentId]
                        ]
                    ]
                );
        }

        $sortedFilters = [];

        //Make Autogenrated filter above all
        foreach ($filtersArr as $filterKey => $filter) {
            if ($filter['auto_generated'] == 1) {
                array_push($sortedFilters, $filter);
            }
        }

        //Make System Filters after Autogenerated
        foreach ($filtersArr as $filterKey => $filter) {
            if ($filter['filter_type'] == 0) {
                array_push($sortedFilters, $filter);
            }
        }

        foreach ($sortedFilters as &$filter) {
            $filter['shared'] = '0';

            $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];
        }

        return $sortedFilters;
    }

    protected function addShowAllFilter(int $componentId)
    {
        $component = $this->modules->components->getById($componentId);

        if ($component && $component['route'] === 'system/notifications') {
            $this->addFilterForNotifications($component);

            return;
        }

        if ($component && $component['route'] === 'system/email/queue') {
            $this->addFilterForEmailQueue($component);
        }

        $this->addFilter(
            [
                'name'              => 'Show All ' . $component['name'],
                'conditions'        => '',
                'component_id'      => $componentId,
                'filter_type'       => 0,//System
                'is_default'        => 1,
                'auto_generated'    => 1,
                'account_id'        => 0
            ]
        );

        if ($component && $component['route'] === 'system/workers/jobs') {
            $this->addFilterForWorkersJobs($component);
        }
    }

    protected function addFilterForNotifications($component)
    {
        $conditions =
            [
                'all_unread'=> '-|app_id|equals|' . $this->app['id'] . '&and|account_id|equals|' . $this->access->auth->account()['id'] . '&and|read|equals|0&and|archive|equals|0&',
                'read'      => '-|app_id|equals|' . $this->app['id'] . '&and|account_id|equals|' . $this->access->auth->account()['id'] . '&and|read|equals|1&and|archive|equals|0&',
                'archive'   => '-|app_id|equals|' . $this->app['id'] . '&and|account_id|equals|' . $this->access->auth->account()['id'] . '&and|archive|equals|1&'
            ];

        foreach ($conditions as $conditionKey => $condition) {
            if ($this->config->databasetype === 'db') {
                $filterCondition =
                    [
                        'conditions'    => 'conditions = :conditions:',
                        'bind'          =>
                            [
                                'conditions'   => $condition
                            ]
                    ];
            } else {
                $filterCondition = ['conditions' => ['conditions', '=', $condition]];
            }

            $filter = $this->getByParams($filterCondition);

            if (!$filter) {
                if ($conditionKey === 'all_unread') {
                    $name = 'Show All Unread ' . $component['name'];
                    $default = 1;
                } else if ($conditionKey === 'read') {
                    $name = 'Show All Read ' . $component['name'];
                    $default = 0;
                } else if ($conditionKey === 'archive') {
                    $name = 'Show All Archived ' . $component['name'];
                    $default = 0;
                }

                $this->addFilter(
                    [
                        'name'              => $name,
                        'conditions'        => $condition,
                        'component_id'      => $component['id'],
                        'filter_type'       => 0,//System
                        'is_default'        => $default,
                        'auto_generated'    => 1,
                        'account_id'        => 0
                    ]
                );
            }
        }
    }

    protected function addFilterForEmailQueue($component)
    {
        $conditions =
            [
                'all_in_queue'      => '-|status|equals|1&',
                'all_sent'          => '-|status|equals|2&',
                'all_errors'        => '-|status|equals|3&',
                'high_prioriry'     => '-|priority|equals|1&'
            ];

        foreach ($conditions as $conditionKey => $condition) {
            if ($this->config->databasetype === 'db') {
                $filterCondition =
                    [
                        'conditions'    => 'conditions = :conditions:',
                        'bind'          =>
                            [
                                'conditions'   => $condition
                            ]
                    ];
            } else {
                $filterCondition = ['conditions' => ['conditions', '=', $condition]];
            }

            $filter = $this->getByParams($filterCondition);

            if (!$filter) {
                if ($conditionKey === 'all_in_queue') {
                    $name = 'Show All In Queue Emails';
                    $default = 1;
                } else if ($conditionKey === 'all_sent') {
                    $name = 'Show All Sent Emails';
                    $default = 0;
                } else if ($conditionKey === 'all_errors') {
                    $name = 'Show All Queue Errors';
                    $default = 0;
                } else if ($conditionKey === 'high_prioriry') {
                    $name = 'Show All High Priority Emails';
                    $default = 0;
                } else if ($conditionKey === 'all') {
                    $name = 'Show All Emails';
                    $default = 0;
                }

                $this->addFilter(
                    [
                        'name'              => $name,
                        'conditions'        => $condition,
                        'component_id'      => $component['id'],
                        'filter_type'       => 0,//System
                        'is_default'        => $default,
                        'auto_generated'    => 1,
                        'account_id'        => 0
                    ]
                );
            }
        }
    }

    protected function addFilterForWorkersJobs($component)
    {
        $conditions =
            [
                'all_scheduled_and_running'      => '-|status|equals|1&OR|status|equals|2&',
                'all_success'                    => '-|status|equals|3&',
                'all_errors'                     => '-|status|equals|4&',
                'all_user_jobs'                  => '-|type|equals|1&'
            ];

        foreach ($conditions as $conditionKey => $condition) {
            if ($this->config->databasetype === 'db') {
                $filterCondition =
                    [
                        'conditions'    => 'conditions = :conditions:',
                        'bind'          =>
                            [
                                'conditions'   => $condition
                            ]
                    ];
            } else {
                $filterCondition = ['conditions' => ['conditions', '=', $condition]];
            }

            $filter = $this->getByParams($filterCondition);

            if (!$filter) {
                if ($conditionKey === 'all_scheduled_and_running') {
                    $name = 'Show All Scheduled and Running Jobs';
                    $default = 0;
                } else if ($conditionKey === 'all_success') {
                    $name = 'Show All Success Jobs';
                    $default = 0;
                } else if ($conditionKey === 'all_errors') {
                    $name = 'Show All Errors Jobs';
                    $default = 0;
                } else if ($conditionKey === 'all_user_jobs') {
                    $name = 'Show All Jobs Scheduled By Users';
                    $default = 0;
                } else if ($conditionKey === 'all') {
                    $name = 'Show All Jobs';
                    $default = 0;
                }

                $this->addFilter(
                    [
                        'name'              => $name,
                        'conditions'        => $condition,
                        'component_id'      => $component['id'],
                        'filter_type'       => 0,//System
                        'is_default'        => $default,
                        'auto_generated'    => 1,
                        'account_id'        => 0
                    ]
                );
            }
        }
    }

    public function addFilter(array $data)
    {
        if (!isset($data['filter_type'])) {
            $data['filter_type'] = 0;
        }
        if (!isset($data['is_default'])) {
            $data['is_default'] = 0;
        }
        if (!isset($data['auto_generated'])) {
            $data['auto_generated'] = 0;
        }
        if (!isset($data['account_id'])) {
            if ($data['filter_type'] !== 0) {
                $account = $this->access->auth->account();

                if ($account) {
                    $data['account_id'] = $account['id'];
                } else {
                    $data['account_id'] = 0;
                }
            } else {
                $data['account_id'] = 0;
            }
        }

        if ($this->checkDefaultFilter($data)) {
            $add = $this->add($data);

            if ($add) {
                $account = $this->access->auth->account();

                if ($account) {
                    $this->packagesData->filters = $this->getFilters($data['component_id'], $account);
                } else {
                    $this->packagesData->filters = $this->getFilters($data['component_id']);
                }
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Added';

                return true;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot add filter.';

        return false;
    }

    public function updateFilter(array $data)
    {
        $component = $this->modules->components->getById($data['component_id']);

        if ($this->checkDefaultFilter($data)) {
            if (isset($data['shared_ids']) && is_string($data['shared_ids'])) {
                $data['shared_ids'] = $this->helper->decode($data['shared_ids'], true);
            }

            if (isset($data['shared_ids']) && is_array($data['shared_ids'])) {
                //Convert EmployeeIds to AccountIds
                $data['shared_ids']['aids'] = [];
                // if (isset($data['shared_ids']['eids']) && count($data['shared_ids']['eids']) > 0) {
                //     $employees = $this->usePackage(Employees::class);

                //     foreach ($data['shared_ids']['eids'] as $eidKey => $eid) {
                //         $searchEmployee = $employees->getById($eid);

                //         if ($searchEmployee) {
                //             array_push($data['shared_ids']['aids'], $searchEmployee['id']);
                //         }
                //     }
                //     unset($data['shared_ids']['eids']);
                // }
                $data['shared_ids'] = $this->helper->encode($data['shared_ids']);
            }

            $update = $this->update($data);

            if ($update) {
                $account = $this->access->auth->account();

                if ($account) {
                    $this->packagesData->filters = $this->getFiltersForAccountAndComponent($account, $data['component_id']);
                } else {
                    $this->packagesData->filters = $this->getFiltersForComponent($data['component_id']);
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Updated';

                return true;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot update filter.';

        return false;
    }

    public function removeFilter(array $data)
    {
        $filter = $this->getById($data['id']);

        if ($filter['auto_generated'] == 1) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove auto generated filter.';

            return false;
        }

        $remove = $this->remove($data['id']);

        if ($remove) {

            if (isset($data['component_id'])) {
                $account = $this->access->auth->account();

                if ($account) {
                    $this->packagesData->filters = $this->getFiltersForAccountAndComponent($account, $data['component_id']);
                } else {
                    $this->packagesData->filters = $this->getFiltersForComponent($data['component_id']);
                }
            }
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Filter Removed';

            return true;
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot remove filter.';

        return false;
    }

    public function cloneFilter(array $data)
    {
        $filter = $this->getById($data['id']);
        $filter['filter_type'] = 1;
        $filter['is_default'] = 0;
        $filter['shared_ids'] = null;

        $account = $this->access->auth->account();

        if ($account) {
            $filter['account_id'] = $account['id'];
        }

        $clone = $this->clone($data['id'], 'name', $filter);

        if ($clone) {
            $account = $this->access->auth->account();

            if ($account) {
                $this->packagesData->filters = $this->getFiltersForAccountAndComponent($account, $data['component_id']);
            } else {
                $this->packagesData->filters = $this->getFiltersForComponent($data['component_id']);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Filter cloned successfully';

            return true;
        }

        return false;
    }

    public function getDefaultFilter(int $componentId)
    {
        $account = $this->access->auth->account();

        if ($account) {
            if ($this->config->databasetype === 'db') {
                $params =
                    [
                        'conditions'    => 'component_id = :cid: AND is_default = :isd: AND account_id = :aid:',
                        'bind'          =>
                            [
                                'cid'   => $componentId,
                                'isd'   => '1',
                                'aid'   => $account['id']
                            ]
                    ];
            } else {
                $params = ['conditions' => [['component_id', '=', $componentId], ['is_default', '=', 1], ['account_id', '=', $account['id']]]];
            }
        } else {
            if ($this->config->databasetype === 'db') {
                $params =
                    [
                        'conditions'    => 'component_id = :cid: AND is_default = :isd:',
                        'bind'          =>
                            [
                                'cid'   => $componentId,
                                'isd'   => '1'
                            ]
                    ];
            } else {
                $params = ['conditions' => [['component_id', '=', $componentId], ['is_default', '=', 1]]];
            }
        }

        $this->defaultFilter = $this->getByParams($params, true);

        if ($this->defaultFilter) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->defaultFilter = $this->defaultFilter;

            return true;
        }

        $this->packagesData->responseCode = 1;

        return false;
    }

    protected function checkDefaultFilter(array $data)
    {
        if (!isset($data['is_default']) || (isset($data['is_default']) && $data['is_default'] != 1)) {
            return true;
        }

        $this->getDefaultFilter($data['component_id']);

        if ($this->defaultFilter) {
            if (isset($data['id']) && $data['id'] !== '') {

                if ($data['id'] !== $this->defaultFilter[0]['id']) {
                    $this->defaultFilter[0]['is_default'] = 0;

                    if (!$this->update($this->defaultFilter[0])) {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error removing default filter';

                        return false;
                    }
                }
            } else {
                $this->defaultFilter[0]['is_default'] = 0;

                if (!$this->update($this->defaultFilter[0])) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error removing default filter';

                    return false;
                }
            }
        }

        return true;
    }
}