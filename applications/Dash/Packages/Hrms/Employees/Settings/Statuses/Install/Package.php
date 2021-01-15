<?php

namespace Applications\Dash\Packages\Hrms\Employees\Settings\Statuses\Install;

use Applications\Dash\Packages\Hrms\Employees\Settings\Statuses\EmployeesStatuses;
use Applications\Dash\Packages\Hrms\Employees\Settings\Statuses\Install\Schema\EmployeesStatuses as EmployeesStatusesSchema;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $schemaToUse = EmployeesStatusesSchema::class;

    protected $packageToUse = EmployeesStatuses::class;

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
                $this->createTable('hrms_employees_statuses', '', (new $this->schemaToUse)->columns(), $dropTables);
            } else {
                $this->createTable('hrms_employees_statuses', '', (new $this->schemaToUse)->columns());
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
        $packagePath = '/applications/Dash/Packages/Hrms/Employees/Settings/Statuses';

        $jsonFile =
            Json::decode($this->localContent->read($packagePath . '/Install/package.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading package.json at location ' . $packagePath);
        }

        $jsonFile['display_name'] = $jsonFile['displayName'];
        $jsonFile['settings'] = Json::encode($jsonFile['settings']);
        $jsonFile['applications'] = Json::encode([$this->init()->application['id'] => ['enabled' => true]]);
        $jsonFile['files'] = Json::encode($this->getInstalledFiles($packagePath));
        $jsonFile['updated_by'] = 0;
        $jsonFile['installed'] = 1;

        $this->modules->packages->add($jsonFile);
        $this->logger->log->info('Package ' . $jsonFile['display_name'] . ' installed successfully on application ' . $this->application['name']);
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