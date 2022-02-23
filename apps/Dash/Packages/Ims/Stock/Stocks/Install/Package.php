<?php

namespace Apps\Dash\Packages\Ims\Stock\Stocks\Install;

use Apps\Dash\Packages\Ims\Stock\Stocks\Install\Schema\ImsStockStocks;
use Apps\Dash\Packages\Ims\Stock\Stocks\Stocks;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Stocks::class;

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
                    $this->createTable('ims_stock_stocks', '', (new ImsStockStocks)->columns(), $dropTables);
                } catch (\Exception $e) {
                    var_dump($e);die();
                }
            } else {
                $this->createTable('ims_stock_stocks', '', (new ImsStockStocks)->columns());
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
        $packagePath = '/apps/Dash/Packages/Ims/Stock/Stocks';

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
}