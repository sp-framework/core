<?php

namespace Apps\Dash\Packages\Crms\Customers\Install;

use Apps\Dash\Packages\Crms\Customers\Customers;
use Apps\Dash\Packages\Crms\Customers\Install\Schema\CrmsCustomers;
use Apps\Dash\Packages\Crms\Customers\Install\Schema\CrmsCustomersFinancialDetails;
use Phalcon\Db\Column;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Customers::class;

    public $suppliers;

    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        if (!$dropTables && $this->checkPackage($this->packageToUse)) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        try {
            if ($dropTables) {
                $this->createTable('crms_customers', '', (new CrmsCustomers)->columns(), $dropTables);
                $this->addIndex('crms_customers', (new CrmsCustomers)->indexes());
                $this->createTable('crms_customers_financial_details', '', (new CrmsCustomersFinancialDetails)->columns(), $dropTables);
            } else {
                $this->createTable('crms_customers', '', (new CrmsCustomers)->columns());
                $this->createTable('crms_customers_financial_details', '', (new CrmsCustomersFinancialDetails)->columns());
            }

            // $this->registerPackage();

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }

    }

    protected function registerPackage()
    {
        $packagePath = '/apps/Dash/Packages/Ims/Suppliers/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' enabled successfully on app ' . $this->app['name']);
    }

    public function updatePackage()
    {
        $this->init();

        // $this->alterTable(
        //     'add',
        //     'crms_customers',
        //     [
        //         new Column(
        //             'added',
        //             [
        //                 'type'          => Column::TYPE_VARCHAR,
        //                 'size'          => 1024,
        //                 'notNull'       => false,
        //                 'comment'       => 'ADDED'
        //             ]
        //         ),
        //         new Column(
        //             'added_2',
        //             [
        //                 'type'          => Column::TYPE_VARCHAR,
        //                 'size'          => 1024,
        //                 'notNull'       => false,
        //                 'comment'       => 'ADDED 2'
        //             ]
        //         )
        //     ]
        // );

        // $this->alterTable(
        //     'modify',
        //     'crms_customers',
        //     [
        //         new Column(
        //             'added',
        //             [
        //                 'type'          => Column::TYPE_VARCHAR,
        //                 'size'          => 100,
        //                 'notNull'       => false,
        //                 'comment'       => 'ADDED'
        //             ]
        //         ),
        //         new Column(
        //             'added_2',
        //             [
        //                 'type'          => Column::TYPE_VARCHAR,
        //                 'size'          => 100,
        //                 'notNull'       => false,
        //                 'comment'       => 'ADDED 2'
        //             ]
        //         )
        //     ]
        // );
        //
        // $this->alterTable(
        //     'drop',
        //     'crms_customers',
        //     [
        //         'added',
        //         'added_2',
        //     ]
        // );

        // $this->dropIndex('crms_customers', 'column_contact_mobile_index');
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