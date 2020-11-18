<?php

namespace Applications\Admin\Components\Filters;

use Applications\Admin\Packages\Filters\Filters;
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

        $this->generateDTContent(Filters::class, 'filters/view', null, ['name'], true, ['name'], $controlActions, null, null, 'name');
    }
}