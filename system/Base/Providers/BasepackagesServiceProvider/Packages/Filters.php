<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

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
        if ($account['id']) {

            $filtersArr =
                $this->getByParams(
                    [
                        'conditions'    => 'component_id = :cid: AND (account_id = :aid: OR account_id = :aid0:)',
                        'bind'          => [
                            'cid'       => $componentId,
                            'aid'       => $account['id'],
                            'aid0'       => 0
                        ]
                    ]
                );

            foreach ($filtersArr as $filterKey => $filter) {
                $filters[$filter['id']] = $filter;
            }

            $sharedFiltersArr =
                $this->getByParams(
                    [
                        'conditions'    => 'shared_ids IS NOT NULL'
                    ]
                );

            foreach ($sharedFiltersArr as $filterKey => $filter) {
                $filter['shared_ids'] = Json::decode($filter['shared_ids'], true);

                if (isset($filter['shared_ids']['rids'])) {
                    if (Arr::has($filter['shared_ids']['rids'], $account['role_id'])) {
                        $filter['account_name'] = $account['email'];
                        $filter['shared_ids'] = Json::encode($filter['shared_ids']);
                        $sharedFilters[$filter['id']] = $filter;
                    }
                } else if (isset($filter['shared_ids']['uids'])) {
                    if (Arr::has($filter['shared_ids']['uids'], $account['id'])) {
                        $filter['account_name'] = $account['email'];
                        $filter['shared_ids'] = Json::encode($filter['shared_ids']);
                        $sharedFilters[$filter['id']] = $filter;
                    }
                }
            }

            return array_merge($filters, $sharedFilters);
        }
        return
            $this->getByParams(
                [
                    'conditions'    => 'component_id = :cid:',
                    'bind'          => [
                        'cid'       => $componentId
                    ]
                ]
            );
    }

    protected function addShowAllFilter(int $componentId)
    {
        $component = $this->modules->components->getById($componentId);

        $this->addFilter(
            [
                'name'              => 'Show All ' . $component['name'],
                'conditions'        => '',
                'component_id'      => $componentId,
                'type'              => 0,//System
                'is_default'        => 0,
                'auto_generated'    => 1,
                'account_id'        => 0
            ]
        );
    }

    public function addFilter(array $data)
    {
        if (!isset($data['type'])) {
            $data['type'] = 0;
        }
        if (!isset($data['is_default'])) {
            $data['is_default'] = 0;
        }
        if (!isset($data['auto_generated'])) {
            $data['auto_generated'] = 0;
        }
        if (!isset($data['account_id'])) {
            if ($data['type'] !== 0) {
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
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Added';

                $this->setFilterUrl($data);

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

            if (isset($data['shared_ids']) && is_array($data['shared_ids'])) {
                $data['shared_ids'] = Json::encode($data['shared_ids']);
            }

            $update = $this->update($data);

            if ($update) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Updated';

                $this->setFilterUrl($data);

                return true;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot update filter.';

        return false;
    }

    public function removeFilter(array $data)
    {
        $remove = $this->remove($data['id']);

        if ($remove) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Filter Removed';

            if (isset($data['component_id'])) {
                $this->setFilterUrl($data);
            }

            return true;
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot remove filter.';

        return false;
    }

    public function cloneFilter(array $data)
    {
        $filter = $this->getById($data['id']);
        $filter['type'] = 1;
        $filter['is_default'] = 0;

        $account = $this->auth->account();

        if ($account) {
            $filter['account_id'] = $account['id'];
        }

        $clone = $this->clone($data['id'], 'name', $filter);

        if ($clone) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Filter cloned successfully';

            $this->setFilterUrl($data);

            return true;
        }

        return false;
    }

    protected function setFilterUrl(array $data)
    {
        $component = $this->modules->components->getById($data['component_id']);

        $filtersArr = $this->getFiltersForComponent($data['component_id']);

        $filters = [];

        foreach ($filtersArr as $key => $filter) {
            $filters[$key] = $filter;
            $filters[$key]['url'] = $this->links->url($component['route']) . '/q/filter/' . $filter['id'];
        }

        $this->packagesData->filters = $filters;
    }

    public function getDefaultFilter(int $componentId)
    {
        $params =
            [
                'conditions'    => 'component_id = :cid: AND is_default = :isd:',
                'bind'          =>
                    [
                        'cid'   => $componentId,
                        'isd'   => '1'
                    ]
            ];

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