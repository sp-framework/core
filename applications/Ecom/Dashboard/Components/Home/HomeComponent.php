<?php

namespace Applications\Ecom\Dashboard\Components\Home;

use Applications\Ecom\Dashboard\Components\Inventory\Brands\Install\Component as BrandsComponent;
use Applications\Ecom\Dashboard\Components\Inventory\Categories\Install\Component as CategoriesComponent;
use Applications\Ecom\Dashboard\Components\Inventory\Suppliers\Install\Component as SuppliersComponent;
use Applications\Ecom\Dashboard\Components\Storages\Install\Component as StoragesComponent;
use Applications\Ecom\Dashboard\Components\Channels\Install\Component as ChannelsComponent;
use Applications\Ecom\Dashboard\Packages\Channels\Install\Package as ChannelsPackage;
use Applications\Ecom\Dashboard\Packages\ABNLookup\Install\Package as ABNLookupPackage;
use Applications\Ecom\Dashboard\Packages\Inventory\Brands\Install\Package as BrandsPackage;
use Applications\Ecom\Dashboard\Packages\Inventory\Categories\Install\Package as CategoriesPackage;
use Applications\Ecom\Dashboard\Packages\Inventory\Suppliers\Install\Package as SuppliersPackage;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // For Installing ABNLookup
        // $abnLookupPackage = new ABNLookupPackage();
        // $abnLookupPackage->installPackage();
        // For Installing Suppliers
        // $suppliersComponent = new SuppliersComponent();
        // $suppliersComponent->installComponent();
        // For Installing Suppliers Package
        // $suppliersPackage = new SuppliersPackage();
        // $suppliersPackage->installPackage(true);
        // For Installing Storage
        // $storagesComponent = new StoragesComponent();
        // $storagesComponent->installComponent();

        // For Installing Brands
        // $brandsComponent = new BrandsComponent();
        // $brandsComponent->installComponent();
        // For Installing Brands Package
        // $brandsPackage = new BrandsPackage();
        // $brandsPackage->installPackage(true);

        // For Installing Channels
        // $channelsComponent = new ChannelsComponent();
        // $channelsComponent->installComponent();
        // For Installing Channels Package
        // $channelsPackage = new ChannelsPackage();
        // $channelsPackage->installPackage(true);


        // For Installing Categories
        // $categoriesComponent = new CategoriesComponent();
        // $categoriesComponent->installComponent();
        // For Installing Categories Package
        // $categoriesPackage = new CategoriesPackage();
        // $categoriesPackage->installPackage(true);

        // $this->view->disable();
    }
}