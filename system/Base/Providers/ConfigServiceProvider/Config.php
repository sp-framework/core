<?php

namespace System\Base\Providers\ConfigServiceProvider;

use Phalcon\Config as PhalconConfig;
use Phalcon\Config\Adapter\Grouped;
use System\Base\Installer\Components\Setup;

class Config
{
    protected $configs = [];

    protected $session;

    protected $request;

    protected $configsFolder;

    public function __construct($session, $request)
    {
        $this->session = $session;

        $this->request = $request;

        $this->configsFolder = base_path('system/Configs/');

        $this->scanDirForConfigs();
    }

    public function getConfigs()
    {
        $configs = $this->getGroupedConfigs()->toArray();

        if (isset($configs['debug']) &&
            isset($configs['db']) &&
            isset($configs['setup']) && $configs['setup'] === false &&
            !isset($this->request->getPost()['session'])
        ) {
            return new PhalconConfig($configs);
        } else {
            return $this->runSetup($configs);
        }
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

    protected function runSetup($configs)
    {
        if (PHP_SAPI === 'cli') {
            if ($configs['setup'] === true) {
                sleep(10);
            }

            exit();
        }

        require_once base_path('system/Base/Installer/Components/Setup.php');

        (new Setup($this->session))->run();

        exit;
    }
}