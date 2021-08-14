<?php

namespace Apps\Dash\Packages\System\Api\Install;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Package as XeroSyncPackages;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApi;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApiCalls;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApiEbay;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApiGeneric;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApiXero;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Api::class;

    public $api;

    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        // if (!$dropTables && $this->checkPackage($this->packageToUse)) {

        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

        //     return;
        // }

        try {
            if ($dropTables) {
                try {
                    // $this->createTable('system_api_generic', '', (new SystemApiGeneric)->columns(), $dropTables);
                    $this->createTable('system_api_ebay', '', (new SystemApiEbay)->columns(), $dropTables);
                    $this->createTable('system_api_xero', '', (new SystemApiXero)->columns(), $dropTables);
                    $this->createTable('system_api_calls', '', (new SystemApiCalls)->columns(), $dropTables);
                    $this->createTable('system_api', '', (new SystemApi)->columns(), $dropTables);
                } catch (\Exception $e) {
                    var_dump($e);die();
                }
            } else {
                // $this->createTable('system_api_generic', '', (new SystemApiGeneric)->columns());
                $this->createTable('system_api_ebay', '', (new SystemApiEbay)->columns());
                $this->createTable('system_api_xero', '', (new SystemApiEbay)->columns());
                $this->createTable('system_api_calls', '', (new SystemApiCalls)->columns());
                $this->createTable('system_api', '', (new SystemApi)->columns());
            }

            // $this->registerPackage();

            $this->installSyncPackages($dropTables);

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    protected function registerPackage()
    {
        $packagePath = '/apps/Dash/Packages/System/Api';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));
        $jsonFile['updated_by'] = 0;
        $jsonFile['installed'] = 1;

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' installed successfully on app ' . $this->app['name']);
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

    protected function installSyncPackages($dropTables = false)
    {
        $xeroSyncPackage = new XeroSyncPackages;

        $xeroSyncPackage->installPackage($dropTables);
    }
}