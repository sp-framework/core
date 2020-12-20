<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Filters as FiltersModel;

class Filters extends BasePackage
{
    protected $modelToUse = FiltersModel::class;

    protected $packageName = 'filters';

    public $filters;

    public function getFiltersForComponent(int $componentId)
    {
        $filters = $this->getFilters($componentId);

        if ($filters) {
            return $filters;
        } else {
            $this->addShowAllFilter($componentId);

            return $this->getFilters($componentId);
        }
    }

    protected function getFilters(int $componentId)
    {
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
            $data['account_id'] = 0;
        }

        if ($this->checkDefaultFilter($data)) {
            $add = $this->add($data);

            if ($add) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Added';

                $this->packagesData->filters =
                    $filtersArr = $this->getFiltersForComponent($data['component_id']);

                return true;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Cannot add filter.';

        return false;

    }

    public function updateFilter(array $data)
    {
        if ($this->checkDefaultFilter($data)) {
            $update = $this->update($data);

            if ($update) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter Updated';

                $this->packagesData->filters =
                    $filtersArr = $this->getFiltersForComponent($data['component_id']);

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
                    $this->packagesData->filters =
                        $filtersArr = $this->getFiltersForComponent($data['component_id']);
                }

                return true;
        }

        return false;
    }

    public function cloneFilter(array $data)
    {
        $filter = $this->getById($data['id']);
        $filter['type'] = 1;
        $filter['is_default'] = 0;

        if ($this->auth->account) {
            $filter['account_id'] = $this->auth->account['id'];
        }

        $clone = $this->clone($data['id'], 'name', $filter);

        if ($clone) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Filter cloned successfully';

                $this->packagesData->filters =
                    $filtersArr = $this->getFiltersForComponent($data['component_id']);

                return true;
        }

        return false;
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
        if (!isset($data['is_default']) || $data['is_default'] == 1 ) {
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