<?php

namespace Applications\Admin\Components\Home;

use Applications\Admin\Packages\Businesses\Install\Package;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // var_dump($this->basepackages->geoCountries->getById(1));
        // var_dump($this->geodb);

        // $modules = $this->usePackage(ModulesPackage::class);
        // var_dump($this->modules->menus->getMenusForApplication($this->application['id']));
        // $this->view->disable();

        //
        //For Installing Businesses
        // $businessesPackage = new Package();
        // $businessesPackage->installPackage(true);

        // $this->view->disable();
    }
}