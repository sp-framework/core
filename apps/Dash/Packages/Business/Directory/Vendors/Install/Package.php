<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Install;

use Apps\Dash\Packages\Business\Directory\Vendors\Install\Schema\Vendors as VendorsSchema;
use Apps\Dash\Packages\Business\Directory\Vendors\Install\Schema\VendorsFinancialDetails;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = VendorsSchema::class;

    protected $packageToUse = Vendors::class;

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
                $this->createTable('business_directory_vendors', '', (new $this->schemaToUse)->columns(), $dropTables);
                $this->createTable('business_directory_vendors_financial_details', '', (new VendorsFinancialDetails)->columns(), $dropTables);
            } else {
                $this->createTable('business_directory_vendors', '', (new $this->schemaToUse)->columns());
                $this->createTable('business_directory_vendors_financial_details', '', (new VendorsFinancialDetails)->columns());
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