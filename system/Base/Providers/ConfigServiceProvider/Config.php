<?php

namespace System\Base\Providers\ConfigServiceProvider;

use Phalcon\Config\Adapter\Grouped;
use Phalcon\Config\Config as PhalconConfig;
use System\Base\Installer\Components\Setup;

class Config
{
    protected $session;

    protected $request;

    protected $configsFolder;

    protected $domain;

    protected $config;

    protected $configObj;

    public function __construct($session, $request)
    {
        $this->session = $session;

        $this->request = $request;

        $this->configsFolder = base_path('system/Configs/');
    }

    public function getConfig($cliArgvs = null)
    {
        //Get Domain Specific Config
        try {
            if (PHP_SAPI === 'cli') {
                if (isset($cliArgvs[3]) && $cliArgvs[3] !== '') {
                    $domain = explode('=', $cliArgvs[3]);

                    if (isset($domain[0]) && $domain[0] === 'domain' && isset($domain[1])) {
                        $this->config = include($this->configsFolder . ucfirst($domain[1]) . '.php');
                    }
                }
            } else {
                $this->domain = ucfirst($this->request->getServerName());

                $this->config = include($this->configsFolder . $this->domain . '.php');
            }
        } catch (\ErrorException $e) {
            //Try Base Config
            try {
                $this->config = include(base_path('system/Configs/Base.php'));
            } catch (\ErrorException $e) {
                throw new \Exception("Base.php file is missing in Configs directory");
            }
        }

        $this->configObj = new PhalconConfig($this->config);

        if (isset($this->config['setup']) && $this->config['setup'] === false &&
            !isset($this->request->getPost()['session'])
        ) {
            return $this->configObj;
        } else {
            return $this->runSetup();
        }
    }

    protected function runSetup()
    {
        if (PHP_SAPI === 'cli') {
            if ($this->configObj->setup === true) {
                return $this->configObj;
            }

            sleep(10);

            exit();
        }

        require_once base_path('system/Base/Installer/Components/Setup.php');

        if (isset($this->request->getPost()['session']) &&
            isset($this->request->getPost()['composer'])
        ) {
            (new Setup($this->session, $this->configObj, false, $this->domain))->run();
        } else if (isset($this->request->getPost()['session']) &&
                   isset($this->request->getPost()['onlyUpdateDb'])
        ) {
            (new Setup($this->session, $this->configObj, true, $this->domain))->run();
        } else if (isset($this->request->getPost()['session'])) {
            (new Setup($this->session, $this->configObj, false, $this->domain))->run();
        } else {
            (new Setup($this->session, $this->configObj, false, $this->domain))->run();
        }

        exit;
    }
}