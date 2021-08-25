<?php

namespace Apps\Dash\Packages\Crms\Customers\Install;

use Apps\Dash\Packages\Crms\Customers\Customers;
use Apps\Dash\Packages\Crms\Customers\Install\Schema\CrmsCustomers;
use Apps\Dash\Packages\Crms\Customers\Install\Schema\CrmsCustomersFinancialDetails;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Customers::class;

    public $suppliers;

    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        if (!$dropTables && $this->checkPackage($this->packageToUse)) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        try {
            if ($dropTables) {
                $this->createTable('crms_customers', '', (new CrmsCustomers)->columns(), $dropTables);
                $this->createTable('crms_customers_financial_details', '', (new CrmsCustomersFinancialDetails)->columns(), $dropTables);
            } else {
                $this->createTable('crms_customers', '', (new CrmsCustomers)->columns());
                $this->createTable('crms_customers_financial_details', '', (new CrmsCustomersFinancialDetails)->columns());
            }

            // $this->registerPackage();

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }

    }

    protected function registerPackage()
    {
        $packagePath = '/apps/Dash/Packages/Ims/Suppliers/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' enabled successfully on app ' . $this->app['name']);
    }

    public function updatePackage()
    {
        //
    }

    public function deletePackage()
    {

    }

    public function reInstallPackage()
    {
        $this->deletePackage();

        $this->installPackage();
    }
}