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
use Apps\Dash\Components\Devtools\Api\Contracts\Install\Component as ContractsComponent;
use Apps\Dash\Components\Devtools\Api\Enums\Install\Component as EnumsComponent;
use Apps\Dash\Components\System\Api\Install\Component as ApiComponent;
use Apps\Dash\Packages\Business\ABNLookup\Install\Package as ABNLookupPackage;
use Apps\Dash\Packages\Business\Channels\Install\Package as ChannelsPackage;
use Apps\Dash\Packages\Business\Directory\Contacts\Install\Package as ContactsPackage;
use Apps\Dash\Packages\Business\Directory\Vendors\Install\Package as VendorsPackage;
use Apps\Dash\Packages\Business\Entities\Install\Package as EntitiesPackage;
use Apps\Dash\Packages\Business\Locations\Install\Package as LocationsPackage;
use Apps\Dash\Packages\Devtools\Api\Contracts\Install\Package as ContractsPackage;
use Apps\Dash\Packages\Devtools\Api\Enums\Install\Package as EnumsPackage;
use Apps\Dash\Packages\Hrms\Designations\HrmsDesignations;
use Apps\Dash\Packages\Hrms\Designations\Install\Package as EmployeesDesignationsPackage;
use Apps\Dash\Packages\Hrms\Employees\Install\Package as EmployeesPackage;
use Apps\Dash\Packages\Hrms\Skills\Install\Package as SkillsPackage;
use Apps\Dash\Packages\Hrms\Statuses\HrmsStatuses;
use Apps\Dash\Packages\Hrms\Statuses\Install\Package as EmployeesStatusesPackage;
use Apps\Dash\Packages\Ims\Brands\Install\Package as BrandsPackage;
use Apps\Dash\Packages\Ims\Categories\Install\Package as CategoriesPackage;
use Apps\Dash\Packages\Ims\Products\Install\Package as ProductsPackage;
use Apps\Dash\Packages\Ims\Specifications\Install\Package as SpecificationsPackage;
use Apps\Dash\Packages\Ims\Suppliers\Install\Package as SuppliersPackage;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Install\Package as ApiPackage;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // $this->reset();
        // $this->resetTemp();

        // $apiPackage = $this->usePackage(Api::class);

        // $api = $apiPackage->useApi(['api_id' => '2']);

        // $api->checkUserToken();

        // $responseData = $api->packagesData->responseData;

        // // $identity = $api->useService('Identity');
        // $identity = $api->useService('Identityapi');

        // $request = new \Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestRequest;

        // $response = $identity->getUser($request);

        // var_dump($response->individualAccount->toArray());die();
        // $responseData['user_data'] = $response->toArray();

        return false;
    }

    protected function resetTemp()
    {
        $contractsComponent = new ContractsComponent();
        $contractsComponent->installComponent();
        $contractsPackage = new ContractsPackage();
        $contractsPackage->installPackage(true);
        $enumComponent = new EnumsComponent();
        $enumComponent->installComponent();
        $enumPackage = new EnumsPackage();
        $enumPackage->installPackage(true);
    }

    protected function reset()
    {
        // For Installing Brands
        // $brandsComponent = new BrandsComponent();
        // $brandsComponent->installComponent();
        // For Installing Brands Package
        $brandsPackage = new BrandsPackage();
        $brandsPackage->installPackage(true);
        // For Installing Categories
        // $categoriesComponent = new CategoriesComponent();
        // $categoriesComponent->installComponent();
        // For Installing Categories Package
        $categoriesPackage = new CategoriesPackage();
        $categoriesPackage->installPackage(true);
        // For Installing Specifications
        // $specificationComponent = new SpecificationsComponent();
        // $specificationComponent->installComponent();
        // For Installing Specifications Package
        $specificationPackage = new SpecificationsPackage();
        $specificationPackage->installPackage(true);
        // For Installing Suppliers
        // $suppliersComponent = new SuppliersComponent();
        // $suppliersComponent->installComponent();
        // For Installing Suppliers Package
        // $suppliersPackage = new SuppliersPackage();
        // $suppliersPackage->installPackage(true);
        // For Installing Products
        // $productsComponent = new ProductsComponent();
        // $productsComponent->installComponent();
        // For Installing Products Package
        $productsPackage = new ProductsPackage();
        $productsPackage->installPackage(true);

        // For Installing Entities
        $businessesPackage = new EntitiesPackage();
        $businessesPackage->installPackage(true);

        // For Installing Locations
        $locationsPackage = new LocationsPackage();
        $locationsPackage->installPackage(true);

        // For Installing Channels
        $channelsPackage = new ChannelsPackage();
        $channelsPackage->installPackage(true);

        // For Installing Contacts
        $contactsPackage = new ContactsPackage();
        $contactsPackage->installPackage(true);
        // For Installing Vendors
        $vendorsPackage = new VendorsPackage();
        $vendorsPackage->installPackage(true);

        // For Installing Skills
        // $skillsComponent = new SkillsComponent();
        // $skillsComponent->installComponent();
        $skillsPackage = new SkillsPackage();
        $skillsPackage->installPackage(true);

        // For Installing Employees
        $employeesPackage = new EmployeesPackage();
        $employeesPackage->installPackage(true);

        // For Installing Employees Statuses
        $employeesStatusesPackage = new EmployeesStatusesPackage();
        $employeesStatusesPackage->installPackage(true);
        $this->addStatuses();

        // For Installing Designations
        // $employeesDesignationsComponent = new EmployeesDesignationsComponent();
        // $employeesDesignationsComponent->installComponent();
        $employeesDesignationsPackage = new EmployeesDesignationsPackage();
        $employeesDesignationsPackage->installPackage(true);
        $this->addDesignations();

        // For Installing Api (component installation works)
        $apiComponent = new ApiComponent();
        $apiComponent->installComponent();
        // For Installing Api Package
        $apiPackage = new ApiPackage();
        $apiPackage->installPackage(true);
        // For Installing API Generator Dev Tools (component installation works)
        // $contractsComponent = new ContractsComponent();
        // $contractsComponent->installComponent();
        // $contractsPackage = new ContractsPackage();
        // $contractsPackage->installPackage(true);
        // $enumComponent = new EnumsComponent();
        // $enumComponent->installComponent();
        // $enumPackage = new EnumsPackage();
        // $enumPackage->installPackage(true);
    }

    protected function addStatuses()
    {
        $pkg = $this->usePackage(HrmsStatuses::class);

        $pkg->addStatus(['name' => 'Active']);
        $pkg->addStatus(['name' => 'Inactive']);
    }

    protected function addDesignations()
    {
        $pkg = $this->usePackage(HrmsDesignations::class);

        $pkg->addDesignation(['name' => 'Administrators']);
        $pkg->addDesignation(['name' => 'Managers']);
    }
}