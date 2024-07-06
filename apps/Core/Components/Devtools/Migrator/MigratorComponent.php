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
        if (isset($this->getData()['installpackage'])) {
            $redoDb = false;
            if (isset($this->getData()['redodb'])) {
                $redoDb = true;
            }

            $this->migratorPackage->installPackage($redoDb);

            return false;
        }

        $this->view->apis = $this->migratorPackage->getAvailableApis(false, true);
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

    public function importIssuesAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->importIssues($this->postData())) {
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

    public function migrateIssuesAction()
    {
        $this->requestIsPost();

        if ($this->migratorPackage->migrateIssues($this->postData())) {
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