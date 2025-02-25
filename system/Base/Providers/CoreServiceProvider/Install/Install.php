<?php

namespace System\Base\Providers\CoreServiceProvider\Install;

use System\Base\BasePackage;
use System\Base\Installer\Packages\Setup\Schema;
use System\Base\Providers\ModulesServiceProvider\DbInstaller;

class Install extends BasePackage
{
    protected $databases;

    protected $dbInstaller;

    public function init($schemaNames = [])
    {
        $databases = (new Schema)->getSchema($this->core->core['settings']['dev']);

        $this->databases = $databases;

        //Only update 1 database
        if (count($schemaNames) > 0) {
            $schemaNamesDatabase = [];
            foreach ($schemaNames as $schemaName) {
                if (isset($databases[$schemaName])) {
                    $schemaNamesDatabase[$schemaName] = $databases[$schemaName];
                }
            }

            $this->databases = $schemaNamesDatabase;
        }

        $this->dbInstaller = new DbInstaller;

        return $this;
    }

    public function install()
    {
        $this->preInstall();

        $this->installDb();

        $this->postInstall();

        return true;
    }

    public function preInstall()
    {
        //Do version specific update for any future version upgrades.
        // if ($this->core->core['version'] === 'x.x.x') {
            //Do something
        // }

        return true;
    }

    public function installDb()
    {
        $this->dbInstaller->installDb($this->databases);

        return true;
    }

    public function postInstall()
    {
        //Do anything after installation.

        return true;
    }

    public function truncate()
    {
        $this->dbInstaller->truncate($this->databases);
    }
}