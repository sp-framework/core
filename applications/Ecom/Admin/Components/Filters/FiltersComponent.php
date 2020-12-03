<?php

namespace Applications\Ecom\Admin\Components\Filters;

use Applications\Ecom\Admin\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    use DynamicTable;
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'filter'
                ]
            ];
        $this->generateDTContent(
            $this->basepackages->filters,
            'filters/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('filters/list');
    }
}