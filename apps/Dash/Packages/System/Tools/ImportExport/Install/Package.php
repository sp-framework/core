<?php

namespace Apps\Dash\Packages\System\Tools\ImportExport\Install;

use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    public function installPackage()
    {
        $this->init();

        $this->registerPackage();
    }

    protected function registerPackage()
    {
        $packagePath = '/apps/Dash/Packages/System/Tools/ImportExport/';

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
}