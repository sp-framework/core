<?php

namespace System\Base\Providers\ConfigServiceProvider;

use Phalcon\Config as PhalconConfig;
use Phalcon\Config\Adapter\Grouped;

class Config
{
    protected $configs = [];

    protected $configsFolder;

    public function __construct()
    {
        $this->configsFolder = base_path('system/Configs/');

        $this->scanDirForConfigs();
    }

    public function getConfigs()
    {
        return new PhalconConfig($this->getGroupedConfigs()->toArray());
    }

    protected function getGroupedConfigs()
    {
        return new Grouped($this->configs);
    }

    protected function scanDirForConfigs()
    {
        if (count($this->configs) === 0) {
            $configDirContents = scandir($this->configsFolder);

            foreach ($configDirContents as $content) {
                if (!is_dir($content)) {
                    array_push($this->configs, $this->configsFolder . $content);
                }
            }
        }
    }
}