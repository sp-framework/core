<?php

namespace Applications\Dash\Packages\Ims\Products\Install;

use Applications\Dash\Packages\Ims\Products\Products;
use Applications\Dash\Packages\Ims\Products\Install\Schema\Products as ProductsSchema;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = ProductsSchema::class;

    protected $packageToUse = Products::class;

    public $products;

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
                $this->createTable('ims_products', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('ims_products', '', (new $this->schemaToUse)->columns());
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
        $packagePath = '/applications/Dash/Packages/Ims/Products/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['applications'] = Json::encode([$this->init()->application['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' enabled successfully on application ' . $this->application['name']);
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