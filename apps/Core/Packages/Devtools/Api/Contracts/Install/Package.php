<?php

namespace Apps\Core\Packages\Devtools\Api\Contracts\Install;

use Apps\Core\Packages\Devtools\Api\Contracts\Contracts;
use Apps\Core\Packages\Devtools\Api\Contracts\Install\Schema\DevtoolsApiContracts;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Contracts::class;

    public $apigenerator;

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
                try {
                    $this->createTable('devtools_api_contracts', '', (new DevtoolsApiContracts)->columns(), $dropTables);
                } catch (\Exception $e) {
                    var_dump($e);die();
                }
            } else {
                $this->createTable('devtools_api_contracts', '', (new DevtoolsApiContracts)->columns());
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
        // if $droptables We should remove package if already registered
        $packagePath = '/apps/Dash/Packages/Devtools/Api/Contracts';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->basepackages->utils->scanDir($packagePath));
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
}