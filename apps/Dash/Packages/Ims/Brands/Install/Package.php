<?php

namespace Apps\Dash\Packages\Ims\Brands\Install;

use Apps\Dash\Packages\Ims\Brands\Brands;
use Apps\Dash\Packages\Ims\Brands\Install\Schema\Brands as BrandsSchema;
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

        if (!$dropTables && $this->checkPackage($this->packageToUse)) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        try {
            if ($dropTables) {
                $this->createTable('ims_brands', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('ims_brands', '', (new $this->schemaToUse)->columns());
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
        $packagePath = '/apps/Dash/Packages/Ims/Brands/';

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