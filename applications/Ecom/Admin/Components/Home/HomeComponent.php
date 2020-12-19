<?php

namespace Applications\Ecom\Admin\Components\Home;

use Applications\Ecom\Admin\Packages\Businesses\Install\Package as BusinessesPackage;
use Applications\Ecom\Admin\Packages\Channels\Install\Package as ChannelsPackage;
use Applications\Ecom\Admin\Packages\Locations\Install\Package as LocationsPackage;
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
        // For Installing Businesses
        // $businessesPackage = new BusinessesPackage();
        // $businessesPackage->installPackage(true);

        // For Installing Locations
        // $locationsPackage = new LocationsPackage();
        // $locationsPackage->installPackage(true);

        // For Installing Channels
        // $locationsPackage = new ChannelsPackage();
        // $locationsPackage->installPackage(true);

        // $this->view->disable();
    }
}