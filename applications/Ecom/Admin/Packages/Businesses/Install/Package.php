<?php

namespace Applications\Ecom\Admin\Packages\Businesses\Install;

use Applications\Ecom\Admin\Packages\Businesses\Businesses;
use Applications\Ecom\Admin\Packages\Businesses\Install\Schema\Businesses as BusinessesSchema;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = BusinessesSchema::class;

    protected $packageToUse = Businesses::class;

    public $businesses;

    public function installPackage(bool $dropTables = false)
    {
        // $this->init();

        // if ($this->checkPackage($this->packageToUse)) {

        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

        //     return;
        // }

        try {
            if ($dropTables) {
                $this->createTable('businesses', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('businesses', '', (new $this->schemaToUse)->columns());
            }

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }

        // $this->registerPackage();
    }

    protected function registerPackage()
    {
        $packagePath = '/applications/Ecom/Admin/Packages/Businesses/';

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