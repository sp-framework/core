<?php

namespace Apps\Dash\Packages\System\Api\Apis\Simplify\Install;

use Apps\Dash\Packages\System\Api\Apis\Simplify;
use Apps\Dash\Packages\System\Api\Apis\Simplify\Install\Schema\SystemApiSimplify;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Simplify::class;

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
                    $this->createTable('system_api_simplify', '', (new SystemApiSimplify)->columns(), $dropTables);
                } catch (\Exception $e) {
                    var_dump($e);die();
                }
            } else {
                $this->createTable('system_api_simplify', '', (new SystemApiSimplify)->columns());
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
        $packagePath = '/apps/Dash/Packages/System/Api/Apis/Simplify';

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
}