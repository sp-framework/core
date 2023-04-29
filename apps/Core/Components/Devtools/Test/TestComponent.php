<?php

namespace Apps\Core\Components\Devtools\Test;

use Apps\Core\Components\Business\Directory\VendorGroups\Install\Component as VendorGroupsComponent;
use Apps\Core\Components\Devtools\Api\Contracts\Install\Component as ContractsComponent;
use Apps\Core\Components\Devtools\Api\Enums\Install\Component as EnumsComponent;
use Apps\Core\Components\Hrms\Designations\Install\Component as EmployeesDesignationsComponent;
use Apps\Core\Components\Hrms\Skills\Install\Component as SkillsComponent;
use Apps\Core\Components\Ims\Brands\Install\Component as BrandsComponent;
use Apps\Core\Components\Ims\Categories\Install\Component as CategoriesComponent;
use Apps\Core\Components\Ims\Products\Install\Component as ProductsComponent;
use Apps\Core\Components\Ims\Specifications\Install\Component as SpecificationsComponent;
use Apps\Core\Components\Ims\Stock\Purchaseorders\Install\Component as PurchaseOrdersComponent;
use Apps\Core\Components\System\Api\Ebay\Taxonomy\Install\Component as TaxonomyComponent;
use Apps\Core\Components\System\Api\Install\Component as ApiComponent;
use Apps\Core\Components\System\Messenger\MessengerComponent;
use Apps\Core\Packages\Business\ABNLookup\Install\Package as ABNLookupPackage;
use Apps\Core\Packages\Business\Channels\Install\Package as ChannelsPackage;
use Apps\Core\Packages\Business\Directory\Contacts\Install\Package as ContactsPackage;
use Apps\Core\Packages\Business\Directory\VendorGroups\Install\Package as VendorGroupsPackage;
use Apps\Core\Packages\Business\Directory\Vendors\Install\Package as VendorsPackage;
use Apps\Core\Packages\Business\Entities\Install\Package as EntitiesPackage;
use Apps\Core\Packages\Business\Finances\TaxGroups\Install\Package as TaxGroupsPackage;
use Apps\Core\Packages\Business\Finances\Taxes\Install\Package as TaxesPackage;
use Apps\Core\Packages\Business\Locations\Install\Package as LocationsPackage;
use Apps\Core\Packages\Crms\CustomerGroups\Install\Package as CustomerGroupsPackage;
use Apps\Core\Packages\Crms\Customers\Install\Package as CustomersPackage;
use Apps\Core\Packages\Devtools\Api\Contracts\Install\Package as ContractsPackage;
use Apps\Core\Packages\Devtools\Api\Enums\Install\Package as EnumsPackage;
use Apps\Core\Packages\Hrms\Designations\HrmsDesignations;
use Apps\Core\Packages\Hrms\Designations\Install\Package as EmployeesDesignationsPackage;
use Apps\Core\Packages\Hrms\Employees\Install\Package as EmployeesPackage;
use Apps\Core\Packages\Hrms\Skills\Install\Package as SkillsPackage;
use Apps\Core\Packages\Hrms\Statuses\HrmsStatuses;
use Apps\Core\Packages\Hrms\Statuses\Install\Package as EmployeesStatusesPackage;
use Apps\Core\Packages\Ims\Brands\Install\Package as BrandsPackage;
use Apps\Core\Packages\Ims\Categories\Install\Package as CategoriesPackage;
use Apps\Core\Packages\Ims\Products\Install\Package as ProductsPackage;
use Apps\Core\Packages\Ims\Specifications\Install\Package as SpecificationsPackage;
use Apps\Core\Packages\Ims\Stock\PurchaseOrders\Install\Package as PurchaseOrdersPackage;
use Apps\Core\Packages\System\Api\Apis\Xero\Sync\Package as XeroSyncPackage;
use Apps\Core\Packages\System\Api\Ebay\Taxonomy\Install\Package as TaxonomyPackage;
use Apps\Core\Packages\System\Api\Install\Package as ApiPackage;
use Apps\Core\Packages\System\Messenger\Install\Package as MessengerPackage;
use Apps\Core\Packages\System\Tools\ImportExport\Install\Package as ImportExportPackage;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Api;

class TestComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $apiPackage = $this->usePackage(Api::class);

        $api = $apiPackage->useApi(1);

        $gitea = $api->useService('Gitea');

        $request = new \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Operations\UserListReposRestRequest;
        $request->username = 'sp-core';
        $response = $gitea->userListRepos($request);
        var_dump($response->toArray());
        var_dump($api);die();
        // $this->basepackages->emailServices->errorEmailService(
        //                     'Email was added to the queue but, there is no email service associated with this app. Please add a new service ' .
        //                     'and assign it to the app via domains.'
        //                 );
        // $apiPackage = new ApiPackage();
        // $apiPackage->installPackage(true);
        // var_dump($this->auth->account());die();
        // $this->resetTemp();return;

        // $this->reset();return;

        // $this->testScripts();
    }

    protected function resetTemp()
    {
        // $taxonomyComponent = new TaxonomyComponent();
        // $taxonomyComponent->installComponent();
        // $taxonomyPackage = new TaxonomyPackage();
        // $taxonomyPackage->installPackage(true);
        //
        // $contractsComponent = new ContractsComponent();
        // $contractsComponent->installComponent();
        // $contractsPackage = new ContractsPackage();
        // $contractsPackage->installPackage(true);
        // $enumComponent = new EnumsComponent();
        // $enumComponent->installComponent();
        // $enumPackage = new EnumsPackage();
        // $enumPackage->installPackage(true);
        //
        // $productsPackage = new ProductsPackage();
        // $productsPackage->installPackage(true);
        //
        // $categoriesPackage = new CategoriesPackage();
        // $categoriesPackage->installPackage(true);
        //
        // $vendorsPackage = new VendorsPackage();
        // $vendorsPackage->installPackage(true);
        //
        // $locationsPackage = new LocationsPackage();
        // $locationsPackage->installPackage(true);
        //
        // $employeesPackage = new EmployeesPackage();
        // $employeesPackage->installPackage(true);
        //
        // $purchaseOrdersComponent = new PurchaseOrdersComponent();
        // $purchaseOrdersComponent->installComponent();
        // $purchaseOrdersPackage = new PurchaseOrdersPackage();
        // $purchaseOrdersPackage->installPackage(true);
        //
        // $apiPackage = new ApiPackage();
        // $apiPackage->installPackage(true);
        // $xeroSyncPackage = new XeroSyncPackage();
        // $xeroSyncPackage->installPackage(true);

        // $vendorgroupsComponent = new VendorGroupsComponent();
        // $vendorgroupsComponent->installComponent();
        // $vendorgroupsPackage = new VendorGroupsPackage();
        // $vendorgroupsPackage->installPackage(true);
        // $contactsPackage = new ContactsPackage();
        // $contactsPackage->installPackage(true);
        // $customersPackage = new CustomersPackage();
        // $customersPackage->installPackage(true);
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
        // $apiComponent = new ApiComponent();
        // $apiComponent->installComponent();
        // For Installing Api Package
        $apiPackage = new ApiPackage();
        $apiPackage->installPackage(true);

        // $taxonomyComponent = new TaxonomyComponent();
        // $taxonomyComponent->installComponent();
        $taxonomyPackage = new TaxonomyPackage();
        $taxonomyPackage->installPackage(true);

        // For Installing API Generator Dev Tools (component installation works)
        // $contractsComponent = new ContractsComponent();
        // $contractsComponent->installComponent();
        $contractsPackage = new ContractsPackage();
        $contractsPackage->installPackage(true);
        // $enumComponent = new EnumsComponent();
        // $enumComponent->installComponent();
        $enumPackage = new EnumsPackage();
        $enumPackage->installPackage(true);

        // $purchaseOrdersComponent = new PurchaseOrdersComponent();
        // $purchaseOrdersComponent->installComponent();
        $purchaseOrdersPackage = new PurchaseOrdersPackage();
        $purchaseOrdersPackage->installPackage(true);

        // $messengerComponent = new MessengerComponent();
        // $messengerComponent->installComponent();
        $messengerPackage = new MessengerPackage();
        $messengerPackage->installPackage(true);

        // $vendorgroupsComponent = new VendorGroupsComponent();
        // $vendorgroupsComponent->installComponent();
        $vendorgroupsPackage = new VendorGroupsPackage();
        $vendorgroupsPackage->installPackage(true);

        // For Installing Vendors
        $vendorsPackage = new VendorsPackage();
        $vendorsPackage->installPackage(true);

        // $taxesComponent = new TaxesComponent();
        // $taxesComponent->installComponent();
        $taxesPackage = new TaxesPackage();
        $taxesPackage->installPackage(true);

        // $taxgroupsComponent = new TaxGroupsComponent();
        // $taxgroupsComponent->installComponent();
        $taxgroupsPackage = new TaxGroupsPackage();
        $taxgroupsPackage->installPackage(true);

        $vendorgroupsPackage = new CustomerGroupsPackage();
        $vendorgroupsPackage->installPackage(true);

        // For Installing Customers
        $customersPackage = new CustomersPackage();
        $customersPackage->installPackage(true);
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

    protected function testScripts()
    {
        // $parser = new \Phalcon\Security\JWT\Token\Parser();

        // $tokenObject = $parser->parse("eyJhbGciOiJSUzI1NiIsImtpZCI6IjFDQUY4RTY2NzcyRDZEQzAyOEQ2NzI2RkQwMjYxNTgxNTcwRUZDMTkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJISy1PWm5jdGJjQW8xbkp2MENZVmdWY09fQmsifQ.eyJuYmYiOjE2MTYxNTY3NDAsImV4cCI6MTYxNjE1ODU0MCwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS54ZXJvLmNvbSIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHkueGVyby5jb20vcmVzb3VyY2VzIiwiY2xpZW50X2lkIjoiODRBQ0ZBRTRCREFGNDM3MkJFMTlBQTQ2Njg0QTkyNzIiLCJzdWIiOiIyMTYzNTc4ZmJmZTg1NDMwOGVjZmYzMmVmODUwNGU4MyIsImF1dGhfdGltZSI6MTYxNjE1NjY1NSwieGVyb191c2VyaWQiOiI0YTY2ZTdhNi1lMGNhLTQ3Y2UtOWZlZS04OGU3YWZiY2UxZDYiLCJnbG9iYWxfc2Vzc2lvbl9pZCI6IjI4M2Q3Y2Y0Y2I1YjQzN2FiYTJjODE5Mzg2YzY1NzJjIiwianRpIjoiZTRlZTJiYTlkNjAyNjhjNjdiMjg4MTBiYThjMzU1NGIiLCJhdXRoZW50aWNhdGlvbl9ldmVudF9pZCI6ImJjZjQ1MzQwLTVhMjctNDQwYS1iMmZlLTg1NTAxOWQxNmQ2MCIsInNjb3BlIjpbImVtYWlsIiwicHJvZmlsZSIsIm9wZW5pZCIsImFjY291bnRpbmcucmVwb3J0cy5yZWFkIiwiZmlsZXMiLCJwYXlyb2xsLmVtcGxveWVlcyIsInBheXJvbGwucGF5cnVucyIsInBheXJvbGwucGF5c2xpcCIsInBheXJvbGwudGltZXNoZWV0cyIsInByb2plY3RzIiwiYWNjb3VudGluZy5zZXR0aW5ncyIsImFjY291bnRpbmcuYXR0YWNobWVudHMiLCJhY2NvdW50aW5nLnRyYW5zYWN0aW9ucyIsImFjY291bnRpbmcuam91cm5hbHMucmVhZCIsImFzc2V0cyIsImFjY291bnRpbmcuY29udGFjdHMiLCJwYXlyb2xsLnNldHRpbmdzIiwib2ZmbGluZV9hY2Nlc3MiXX0.INuzO7I6_82EOnT1Kj4N4TzZFrxx4T7X8_z94p5jVg0TYufbDfIt8Xqld09gtj8SfOWIN4iQdkG5TTzakZ86JgNNg6Azca-K6mcGZVwBIw4iaNGkimt3THHJpBsL8SJYMWec_tor47yhvr6s7jJ7t_WIx6rLO5IG11upGfkvK9QC-nDLwrBLgVu5RVVKeBN3hCHpo9eDaRPnE5jIuKY98eYr9HtEFxMos2XPB2c_MO8p9YmO_DWZ1F44-wmkJc265iGcuaHebDax67noXmpV4KbdWFOgb4scCxTgIru-uUMLTh0dHLgeWwAOBHlJcMFBTEwgY9roz_bJrlnC_N4Axw");

        // var_dump($tokenObject->getClaims());
        // var_dump($tokenObject->getClaims()->get('authentication_event_id'));


        $apiPackage = $this->usePackage(Api::class);

        $api = $apiPackage->useApi(['api_id' => '3']);

        $responseData = $api->packagesData->responseData;

        // $taxonomy = $api->useService('Taxonomyapi');

        // $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetDefaultCategoryTreeIdRestRequest;

        // $request->marketplace_id = \Apps\Core\Packages\System\Api\Apis\Ebay\Taxonomyapi\Enums\TaxonomyapiEnum::TAXONOMY_EBAY_AU;

        // $response = $taxonomy->getDefaultCategoryTreeId($request);

        // $categoryTreeId = $response->toArray()['categoryTreeId'];

        // All Categories
        // $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategoryTreeRestRequest;

        // $request->category_tree_id = $categoryTreeId;

        // $response = $taxonomy->getCategoryTree($request);

        // $this->localContent->write('private/0/Api/Ebay/Taxonomy/Taxonomy.json', Json::encode($response->toArray()));

        // $categories = $this->localContent->read('private/0/Api/Ebay/Taxonomy/Taxonomy.json');

        // dump(Json::decode($categories, true));

        // Only Vehicles
        // $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySubtreeRestRequest;

        // $request->category_tree_id = $categoryTreeId;

        // $request->category_id = '131090';

        // $response = $taxonomy->getCategorySubtree($request);

        // $this->localContent->write('private/0/Api/Ebay/Taxonomy/Vehicles.json', Json::encode($response->toArray()));

        // $categoriesJson = $this->localContent->read('private/0/Api/Ebay/Taxonomy/Taxonomy.json');

        // $categories = Json::decode($categoriesJson, true);

        // $taxonomy = [];
        // $taxonomy['categoryTreeId'] = $categories['categoryTreeId'];
        // $taxonomy['categoryTreeVersion'] = $categories['categoryTreeVersion'];
        // $this->localContent->write('private/0/Api/Ebay/Taxonomy/Version.json', Json::encode($taxonomy));
        // dump($categories);
        // $taxonomy = [];
        // foreach ($categories['rootCategoryNode']['childCategoryTreeNodes'] as $rootCategoryKey => $rootCategory) {
        //     $rootCategoryArr = [];

        //     $rootCategoryArr[$rootCategory['category']['categoryId']] = $rootCategory;

        //     $this->localContent->write(
        //         'private/0/Api/Ebay/Taxonomy/' . $rootCategory['category']['categoryId'] . '.json', Json::encode($rootCategoryArr)
        //     );
        // }
        // dump(Json::decode($this->localContent->read('private/0/Api/Ebay/Taxonomy/131090.json'), true));
        // $identity = $api->useService('Identityapi');
        // $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestRequest;

        // $inventory = $api->useService('Inventoryapi');

        // var_dump($inventory);
        // $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemsRestRequest;

        // $response = $inventory->getInventoryItems($request);
        // var_dump($response->toArray());

        // $response = $identity->getUser($request);

        // var_dump($response->toArray());die();
        // $responseData['user_data'] = $response->toArray();

        // Tradingapi

        $trading = $api->useService('Tradingapi');

        $request = new \Apps\Core\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreRequest;

        $response = $trading->getStore($request);

        var_dump($response->toArray());
        return false;
    }

    public function addAction()
    {
        //
    }

    public function updateAction()
    {
        //
    }

    public function removeAction()
    {
        //
    }
}