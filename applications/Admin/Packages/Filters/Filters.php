<?php

namespace Applications\Admin\Packages\Filters;

use Applications\Admin\Packages\Filter\Filter;
use Applications\Admin\Packages\Filters\Model\Filters as FiltersModel;
use System\Base\BasePackage;

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
        $filter = $this->usePackage(Filter::class);

        $filter->addFilter(
            [
                'name'          => 'Show All',
                'conditions'    => '',
                'component_id'  => $componentId,
                'permission'    => 0,//System
                'is_default'    => 0
            ]
        );
    }
}