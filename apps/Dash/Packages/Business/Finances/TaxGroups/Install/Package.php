<?php

namespace Apps\Dash\Packages\Business\Finances\TaxGroups\Install;

use Apps\Dash\Packages\Business\Finances\TaxGroups\Install\Schema\BusinessFinancesTaxGroups;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = BusinessFinancesTaxGroups::class;

    public function installPackage(bool $dropTables = false)
    {
        // $this->init();

        // if (!$dropTables && $this->checkPackage($this->packageToUse)) {

        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

        //     return;
        // }

        try {
            if ($dropTables) {
                $this->createTable('business_finances_tax_groups', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('business_finances_tax_groups', '', (new $this->schemaToUse)->columns());
            }

            // $this->registerPackage();

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }
}