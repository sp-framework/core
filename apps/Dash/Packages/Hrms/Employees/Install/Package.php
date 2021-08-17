<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install;

use Apps\Dash\Packages\Hrms\Employees\Employees;
use Apps\Dash\Packages\Hrms\Employees\Install\Schema\HrmsEmployees;
use Apps\Dash\Packages\Hrms\Employees\Install\Schema\HrmsEmployeesContact;
use Apps\Dash\Packages\Hrms\Employees\Install\Schema\HrmsEmployeesEmployment;
use Apps\Dash\Packages\Hrms\Employees\Install\Schema\HrmsEmployeesFinance;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $packageToUse = Employees::class;

    public $employees;

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
                $this->createTable('hrms_employees', '', (new HrmsEmployees)->columns(), $dropTables);
                $this->createTable('hrms_employees_employment', '', (new HrmsEmployeesEmployment)->columns(), $dropTables);
                $this->createTable('hrms_employees_contact', '', (new HrmsEmployeesContact)->columns(), $dropTables);
                $this->createTable('hrms_employees_finance', '', (new HrmsEmployeesFinance)->columns(), $dropTables);
            } else {
                $this->createTable('hrms_employees', '', (new HrmsEmployees)->columns());
                $this->createTable('hrms_employees_employment', '', (new HrmsEmployeesEmployment)->columns());
                $this->createTable('hrms_employees_contact', '', (new HrmsEmployeesContact)->columns());
                $this->createTable('hrms_employees_finance', '', (new HrmsEmployeesFinance)->columns());
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
        $packagePath = '/apps/Ecom/Common/Packages/Hrms/Employees/';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['apps'] = Json::encode([$this->init()->app['id'] => ['installed' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' installed successfully on app ' . $this->app['name']);
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