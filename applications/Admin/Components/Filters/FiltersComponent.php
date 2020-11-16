<?php

namespace Applications\Admin\Components\Filters;

use Applications\Admin\Packages\Filters\Filters;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    public function viewAction()
    {
        // var_dump($this->events);
        // $this->view->disable();
        $this->generateDTContent(Filters::class, 'filters/view');
    }
}