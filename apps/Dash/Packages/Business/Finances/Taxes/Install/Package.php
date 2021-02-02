<?php

namespace Apps\Dash\Packages\Filters\Install;

use Apps\Dash\Packages\Filters\Filters;
use Apps\Dash\Packages\Filters\Install\Schema\Filters as FiltersSchema;
use Apps\Dash\Packages\Module\Install;

class Package extends Install
{
    protected $schemaToUse = FiltersSchema::class;

    protected $packageToUse = Filters::class;

    public $menus;

    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        // if ($this->checkPackage($this->packageToUse)) {

        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

        //     return;
        // }



        // var_dump($dropTables);
        // die();
        try {
            if ($dropTables) {
                $this->createTable('business_finances_taxes', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('business_finances_taxes', (new $this->schemaToUse)->columns());
            }

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
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