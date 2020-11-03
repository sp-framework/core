<?php

namespace Applications\Admin\Packages\Filters\Install;

use Applications\Admin\Packages\Install\Filters\Schema\Filters;
use System\Base\BasePackage;

class Package extends BasePackage
{
    public function installPackage(bool $dropTables = false)
    {
        try {
            if ($dropTables) {
                $this->createTable('filters', (new Filters)->columns(), $dropTables);
            } else {
                $this->createTable('filters', (new Filters)->columns());
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
        //
    }
}