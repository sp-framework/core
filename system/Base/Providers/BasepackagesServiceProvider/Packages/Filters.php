<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Applications\Dash\Packages\Hrms\Employees\Employees;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Filters as FiltersModel;

class Filters extends BasePackage
{
    protected $modelToUse = FiltersModel::class;

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
        return
            $this->getByParams(
                [
                    'conditions'    => 'component_id = :cid: AND auto_generated = :ag:',
                    'bind'          => [
                        'cid'       => $componentId,
                        'ag'        => 1
                    ]
                ]
            );
    }

    protected function getFilters(int $componentId, array $account = null)
    {
        $component = $this->modules->components->getComponentById($componentId);

        $employeesPackage = $this->init()->checkPackage('Applications\Dash\Packages\Hrms\Employees\Employees');

        if ($employeesPackage) {
            $employeesPackage = new \Applications\Dash\Packages\Hrms\Employees\Employees;
        }

        if ($account && isset($account['id'])) {
            $filtersArr =
                $this->getByParams(
                    [
                        'conditions'    => 'component_id = :cid: AND (account_id = :aid: OR account_id = :aid0:)',
                        'bind'          => [
                            'cid'       => $componentId,
                            'aid'       => $account['id'],
                            'aid0'      => 0
                        ]
                    ]
                );

            $myFilters = [];

            //Make System Filters above all
            $filtersArr = msort($filtersArr, 'filter_type');

            foreach ($filtersArr as $filterKey => $filter) {
                $filter['shared'] = '0';

                $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];

                if ($filter['filter_type'] == 1 &&
                    $filter['account_id'] == $account['id']
                ) {
                    array_push($myFilters, $filter['id']);

                    if ($filter['shared_ids']) {//Im Sharing

                        $filter['shared_ids'] = Json::decode($filter['shared_ids'], true);

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

                                if ($employeesPackage) {
                                    $employee = $employeesPackage->searchByAccountId($sharingAid);

                                    if ($employee) {
                                        $filter['shared_ids']['eids'][$sharingAidKey] =
                                            [
                                                'id'    => $sharingAid,
                                                'name'  => $employee['full_name']
                                            ];
                                    }
                                }
                            }
                        }

                        $filter['shared_ids'] = $this->escaper->escapeHtml(Json::encode($filter['shared_ids']));
                    }
                }

                $filters[$filter['id']] = $filter;
            }

            $sharedFiltersArr =
                $this->getByParams(
                    [
                        'conditions'    => 'component_id = :cid: AND shared_ids IS NOT NULL',
                        'bind'          => [
                            'cid'       => $componentId
                        ]
                    ]
                );

            if ($sharedFiltersArr) {//Shared By Others
                foreach ($sharedFiltersArr as $filterKey => $filter) {
                    $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];

                    if (!in_array($filter['id'], $myFilters)) {

                        $filter['shared_ids'] = Json::decode($filter['shared_ids'], true);

                        if (isset($filter['shared_ids']['rids']) &&
                            count($filter['shared_ids']['rids']) > 0
                        ) {
                            foreach ($filter['shared_ids']['rids'] as $ridKey => $rid) {
                                if ($rid == $account['role_id']) {

                                    if ($employeesPackage) {
                                        $employee = $employeesPackage->searchByAccountId($filter['account_id']);

                                        if ($employee) {
                                            $filter['employee_full_name'] = $employee['full_name'];
                                        }
                                    } else {
                                        $sharedBy = $this->accounts->getById($filter['account_id']);

                                        if ($sharedBy) {
                                            $filter['account_email'] = $sharedBy['email'];
                                        }
                                    }

                                    $filter['shared_ids'] = $this->escaper->escapeHtml(Json::encode($filter['shared_ids']));

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

                                    if ($employeesPackage) {
                                        $employee = $employeesPackage->searchByAccountId($filter['account_id']);

                                        if ($employee) {
                                            $filter['employee_full_name'] = $employee['full_name'];
                                        }
                                    } else {
                                        $sharedBy = $this->accounts->getById($filter['account_id']);

                                        if ($sharedBy) {
                                            $filter['account_email'] = $sharedBy['email'];
                                        }
                                    }

                                    $filter['shared_ids'] = $this->escaper->escapeHtml(Json::encode($filter['shared_ids']));

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

                return $filters;
            }

            return $filters;
        }

        $filtersArr =
            $this->getByParams(
                [
                    'conditions'    => 'component_id = :cid:',
                    'bind'          => [
                        'cid'       => $componentId
                    ]
                ]
            );

        //Make System Filters above all
        $filtersArr = msort($filtersArr, 'filter_type');

        foreach ($filtersArr as &$filter) {
            $filter['shared'] = '0';

            $filter['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];
        }

        return $filtersArr;
    }

    protected function addShowAllFilter(int $componentId)
    {
        $component = $this->modules->components->getById($componentId);

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
                $account = $this->auth->account();

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
                $account = $this->auth->account();

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
                $data['shared_ids'] = Json::decode($data['shared_ids'], true);
            }

            if (isset($data['shared_ids']) && is_array($data['shared_ids'])) {
                //Convert EmployeeIds to AccountIds
                $data['shared_ids']['aids'] = [];
                if (isset($data['shared_ids']['eids']) && count($data['shared_ids']['eids']) > 0) {
                    $employees = $this->usePackage(Employees::class);

                    foreach ($data['shared_ids']['eids'] as $eidKey => $eid) {
                        $searchEmployee = $employees->getById($eid);

                        if ($searchEmployee) {
                            array_push($data['shared_ids']['aids'], $searchEmployee['id']);
                        }
                    }
                    unset($data['shared_ids']['eids']);
                }
                $data['shared_ids'] = Json::encode($data['shared_ids']);
            }

            $update = $this->update($data);

            if ($update) {
                $account = $this->auth->account();

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
                $account = $this->auth->account();

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

        $account = $this->auth->account();

        if ($account) {
            $filter['account_id'] = $account['id'];
        }

        $clone = $this->clone($data['id'], 'name', $filter);

        if ($clone) {
            $account = $this->auth->account();

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
        $account = $this->auth->account();

        if ($account) {
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
            $params =
                [
                    'conditions'    => 'component_id = :cid: AND is_default = :isd:',
                    'bind'          =>
                        [
                            'cid'   => $componentId,
                            'isd'   => '1'
                        ]
                ];
        }

        $this->defaultFilter = $this->getByParams($params);

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