<?php

namespace Applications\Core\Admin\Packages\Filters\Install;

use Applications\Core\Admin\Packages\Filters\Filters;
use Applications\Core\Admin\Packages\Filters\Install\Schema\Filters as FiltersSchema;
use Applications\Core\Admin\Packages\Module\Install;

class Package extends Install
{
    protected $schemaToUse = FiltersSchema::class;

    protected $packageToUse = Filters::class;

    public $menus;

    public function installPackage(bool $dropTables = false)
    {
        // if ($this->checkPackage($this->packageToUse)) {

        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

        //     return;
        // }



        // var_dump($dropTables);
        // die();
        try {
            if ($dropTables) {
                $this->createTable('filters', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('filters', (new $this->schemaToUse)->columns());
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