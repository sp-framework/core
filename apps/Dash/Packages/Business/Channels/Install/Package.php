<?php

namespace Apps\Dash\Packages\Business\Channels\Install;

use Apps\Dash\Packages\Business\Channels\Install\Schema\BusinessChannels;
use Apps\Dash\Packages\Business\Channels\Install\Schema\BusinessChannelsEbay;
use Apps\Dash\Packages\Business\Channels\Install\Schema\BusinessChannelsEshop;
use Apps\Dash\Packages\Business\Channels\Install\Schema\BusinessChannelsPos;
use Apps\Dash\Packages\Business\Channels\Install\Schema\Channels as ChannelsSchema;
use Apps\Dash\Packages\Channels\Channels;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = ChannelsSchema::class;

    protected $packageToUse = Channels::class;

    public $channels;

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
                    $this->createTable('business_channels_eshop', '', (new BusinessChannelsEshop)->columns(), $dropTables);
                    $this->createTable('business_channels_ebay', '', (new BusinessChannelsEbay)->columns(), $dropTables);
                    $this->createTable('business_channels_pos', '', (new BusinessChannelsPos)->columns(), $dropTables);
                    $this->createTable('business_channels', '', (new BusinessChannels)->columns(), $dropTables);
                } catch (\Exception $e) {
                    var_dump($e);die();
                }
            } else {
                $this->createTable('business_channels_eshop', '', (new BusinessChannelsEshop)->columns());
                $this->createTable('business_channels_ebay', '', (new BusinessChannelsEbay)->columns());
                $this->createTable('business_channels_pos', '', (new BusinessChannelsPos)->columns());
                $this->createTable('business_channels', '', (new BusinessChannels)->columns());
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
        $packagePath = '/apps/Dash/Packages/Business/Channels/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['installed' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

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