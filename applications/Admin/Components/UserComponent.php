<?php

namespace Applications\Admin\Components;

use Applications\Admin\Packages\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
    public function viewAction()
    {
        $modules = $this->usePackage(ModulesPackage::class);