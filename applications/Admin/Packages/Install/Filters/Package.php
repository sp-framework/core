<?php

namespace Applications\Admin\Packages\Install\Filters;

use Applications\Admin\Packages\Install\Filters\Schema\Filters;
use System\Base\BasePackage;

class Package extends BasePackage
{
    public function installPackage(bool $drop = false)
    {
        try {
            if ($drop) {
                $this->createTable('filters', (new Filters)->columns(), true);
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