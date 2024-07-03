<?php

namespace Apps\Core\Components\Devtools\Migrator;

use Apps\Core\Packages\Devtools\Migrator\DevtoolsMigrator;
use System\Base\BaseComponent;

class MigratorComponent extends BaseComponent
{
    protected $migratorPackage;

    public function initialize()
    {
        $this->migratorPackage = $this->usePackage(DevtoolsMigrator::class);

        $this->setModuleSettings(true);

        $this->setModuleSettingsData([
                'apis' => $this->migratorPackage->getAvailableApis(true, false),
                'apiClients' => $this->migratorPackage->getAvailableApis(false, false)
            ]
        );
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->apis = $this->migratorPackage->getAvailableApis(false, true);

        return;
    }

    public function syncRepositoriesAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->syncRepositories($this->postData())) {
            $this->addResponse(
                $this->migratorPackage->packagesData->responseMessage,
                $this->migratorPackage->packagesData->responseCode,
                $this->migratorPackage->packagesData->responseData
            );

            return;
        }

        $this->addResponse(
            $this->migratorPackage->packagesData->responseMessage,
            $this->migratorPackage->packagesData->responseCode
        );
    }

    public function syncLabelsAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->syncLabels($this->postData())) {
            $this->addResponse(
                $this->migratorPackage->packagesData->responseMessage,
                $this->migratorPackage->packagesData->responseCode,
                $this->migratorPackage->packagesData->responseData
            );

            return;
        }

        $this->addResponse(
            $this->migratorPackage->packagesData->responseMessage,
            $this->migratorPackage->packagesData->responseCode
        );
    }

    public function migrateLabelsAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->migrateLabels($this->postData())) {
            $this->addResponse(
                $this->migratorPackage->packagesData->responseMessage,
                $this->migratorPackage->packagesData->responseCode,
                $this->migratorPackage->packagesData->responseData
            );

            return;
        }

        $this->addResponse(
            $this->migratorPackage->packagesData->responseMessage,
            $this->migratorPackage->packagesData->responseCode
        );
    }

    public function syncMilestonesAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->syncMilestones($this->postData())) {
            $this->addResponse(
                $this->migratorPackage->packagesData->responseMessage,
                $this->migratorPackage->packagesData->responseCode,
                $this->migratorPackage->packagesData->responseData
            );

            return;
        }

        $this->addResponse(
            $this->migratorPackage->packagesData->responseMessage,
            $this->migratorPackage->packagesData->responseCode
        );
    }

    public function migrateMilestonesAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->migrateMilestones($this->postData())) {
            $this->addResponse(
                $this->migratorPackage->packagesData->responseMessage,
                $this->migratorPackage->packagesData->responseCode,
                $this->migratorPackage->packagesData->responseData
            );

            return;
        }

        $this->addResponse(
            $this->migratorPackage->packagesData->responseMessage,
            $this->migratorPackage->packagesData->responseCode
        );
    }
}