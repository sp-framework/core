<?php

namespace Applications\Ecom\Dashboard\Packages\Inventory\Brands\Install;

use Applications\Ecom\Dashboard\Packages\Inventory\Brands\Brands;
use Applications\Ecom\Dashboard\Packages\Inventory\Brands\Install\Schema\Brands as BrandsSchema;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = BrandsSchema::class;

    protected $packageToUse = Brands::class;

    public $brands;

    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        if ($this->checkPackage($this->packageToUse)) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        try {
            if ($dropTables) {
                $this->createTable('brands', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('brands', '', (new $this->schemaToUse)->columns());
            }

            $this->registerPackage();

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }

    }

    protected function registerPackage()
    {
        $packagePath = '/applications/Ecom/Dashboard/Packages/Inventory/Brands/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['applications'] = Json::encode([$this->init()->application['id'] => ['installed' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' installed successfully on application ' . $this->application['name']);
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