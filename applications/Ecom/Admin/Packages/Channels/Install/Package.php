<?php

namespace Applications\Ecom\Admin\Packages\Channels\Install;

use Applications\Ecom\Admin\Packages\Channels\Channels;
use Applications\Ecom\Admin\Packages\Channels\Install\Schema\Channels as ChannelsSchema;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = ChannelsSchema::class;

    protected $packageToUse = Channels::class;

    public $channels;

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
                $this->createTable('channels', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('channels', '', (new $this->schemaToUse)->columns());
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