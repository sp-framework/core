<?php

namespace Apps\Dash\Components\Home;

// use Apps\Dash\Components\Hrms\Designations\Install\Component as EmployeesDesignationsComponent;
// use Apps\Dash\Components\Hrms\Skills\Install\Component as SkillsComponent;
// use Apps\Dash\Components\Ims\Brands\Install\Component as BrandsComponent;
// use Apps\Dash\Components\Ims\Categories\Install\Component as CategoriesComponent;
// use Apps\Dash\Components\Ims\Products\Install\Component as ProductsComponent;
// use Apps\Dash\Components\Ims\Specifications\Install\Component as SpecificationsComponent;
// use Apps\Dash\Components\Ims\Suppliers\Install\Component as SuppliersComponent;
// use Apps\Dash\Components\Storages\Install\Component as StoragesComponent;
use Apps\Dash\Packages\Business\ABNLookup\Install\Package as ABNLookupPackage;
use Apps\Dash\Packages\Business\Entities\Install\Package as EntitiesPackage;
use Apps\Dash\Packages\Business\Channels\Install\Package as ChannelsPackage;
use Apps\Dash\Packages\Business\Locations\Install\Package as LocationsPackage;
use Apps\Dash\Packages\Hrms\Designations\Install\Package as EmployeesDesignationsPackage;
use Apps\Dash\Packages\Hrms\Employees\Install\Package as EmployeesPackage;
use Apps\Dash\Packages\Hrms\Skills\Install\Package as SkillsPackage;
use Apps\Dash\Packages\Hrms\Statuses\Install\Package as EmployeesStatusesPackage;
use Apps\Dash\Packages\Ims\Brands\Install\Package as BrandsPackage;
use Apps\Dash\Packages\Ims\Categories\Install\Package as CategoriesPackage;
use Apps\Dash\Packages\Ims\Products\Install\Package as ProductsPackage;
use Apps\Dash\Packages\Ims\Specifications\Install\Package as SpecificationsPackage;
use Apps\Dash\Packages\Ims\Suppliers\Install\Package as SuppliersPackage;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // For Installing Filters
        // $filtersComponent = new FiltersComponent();
        // $filtersComponent->installComponent();
        // For Installing ABNLookup
        // $abnLookupPackage = new ABNLookupPackage();
        // $abnLookupPackage->installPackage();
        // For Installing Storage
        // $storagesComponent = new StoragesComponent();
        // $storagesComponent->installComponent();

        // For Installing Brands
        // $brandsComponent = new BrandsComponent();
        // $brandsComponent->installComponent();
        // For Installing Brands Package
        $brandsPackage = new BrandsPackage();
        // $brandsPackage->installPackage(true);
        // For Installing Categories
        // $categoriesComponent = new CategoriesComponent();
        // $categoriesComponent->installComponent();
        // For Installing Categories Package
        $categoriesPackage = new CategoriesPackage();
        // $categoriesPackage->installPackage(true);
        // For Installing Specifications
        // $specificationComponent = new SpecificationsComponent();
        // $specificationComponent->installComponent();
        // For Installing Specifications Package
        $specificationPackage = new SpecificationsPackage();
        // $specificationPackage->installPackage(true);
        // For Installing Suppliers
        // $suppliersComponent = new SuppliersComponent();
        // $suppliersComponent->installComponent();
        // For Installing Suppliers Package
        $suppliersPackage = new SuppliersPackage();
        // $suppliersPackage->installPackage(true);
        // For Installing Products
        // $productsComponent = new ProductsComponent();
        // $productsComponent->installComponent();
        // For Installing Products Package
        $productsPackage = new ProductsPackage();
        // $productsPackage->installPackage(true);

        // var_dump($this->basepackages->geoCountries->getById(1));
        // var_dump($this->geodb);

        // $modules = $this->usePackage(ModulesPackage::class);
        // var_dump($this->modules->menus->getMenusForApp($this->app['id']));
        // $this->view->disable();

        //
        // For Installing Entities
        $businessesPackage = new EntitiesPackage();
        // $businessesPackage->installPackage(true);

        // For Installing Locations
        $locationsPackage = new LocationsPackage();
        // $locationsPackage->installPackage(true);

        // For Installing Channels
        $channelsPackage = new ChannelsPackage();
        // $channelsPackage->installPackage(true);

        // For Installing Skills
        // $skillsComponent = new SkillsComponent();
        // $skillsComponent->installComponent();
        $skillsPackage = new SkillsPackage();
        // $skillsPackage->installPackage(true);

        // For Installing Employees
        $employeesPackage = new EmployeesPackage();
        // $employeesPackage->installPackage(true);

        // For Installing Employees Statuses
        $employeesStatusesPackage = new EmployeesStatusesPackage();
        // $employeesStatusesPackage->installPackage(true);
        // $this->view->disable();

        // For Installing Designations
        // $employeesDesignationsComponent = new EmployeesDesignationsComponent();
        // $employeesDesignationsComponent->installComponent();
        $employeesDesignationsPackage = new EmployeesDesignationsPackage();
        // $employeesDesignationsPackage->installPackage(true);
    }
}