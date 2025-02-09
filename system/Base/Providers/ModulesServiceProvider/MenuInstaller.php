<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\BasePackage;

class MenuInstaller extends BasePackage
{
    public function installMenu(array $componentJsonFileArr)
    {
        $menu = $componentJsonFileArr['menu'];

        trace([$menu]);
        return true;
    }
}