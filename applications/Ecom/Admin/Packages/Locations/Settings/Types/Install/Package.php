<?php

namespace Applications\Ecom\Admin\Packages\Locations\Settings\Types\Install;

use Applications\Ecom\Admin\Packages\Locations\Settings\Types\Install\Schema\LocationsTypes as LocationsTypesSchema;
use Applications\Ecom\Admin\Packages\Locations\Settings\Types\LocationsTypes;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = LocationsTypesSchema::class;

    protected $packageToUse = LocationsTypes::class;

    public $types;

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
                $this->createTable('locations_types', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('locations_types', '', (new $this->schemaToUse)->columns());
            }

            // $this->registerPackage();

            $this->addBaseLocationsTypes();

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }

    }

    protected function registerPackage()
    {
        $packagePath = '/applications/Ecom/Admin/Packages/Locations/Settings/Types';

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

    protected function addBaseLocationsTypes()
    {
        $types = ['Shop','Warehouse','Office','Home Office','Storage','Show Grounds'];

        $package = new LocationsTypes();

        foreach ($types as $key => $type) {
            $package->addLocationsType(['name' => $type, 'description' => $type]);
        }
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