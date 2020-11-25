<?php

namespace Applications\Admin\Components\Filters;

use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
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