<?php

namespace Applications\Admin\Components\Filters;

use Applications\Admin\Packages\Filters\Filters;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    public function viewAction()
    {
        $this->generateDTContent(Filters::class, 'filters/view');
    }
}