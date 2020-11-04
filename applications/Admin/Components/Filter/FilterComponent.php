<?php

namespace Applications\Admin\Components\Filter;

use System\Base\BaseComponent;

class FilterComponent extends BaseComponent
{
    public function viewAction()
    {
        var_dump($this->getData());

        $this->view->disable();
        // $modules = $this->usePackage(ModulesPackage::class);
    }
}