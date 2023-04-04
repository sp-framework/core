<?php

namespace Apps\Dash\Components\System\Core;

use System\Base\BaseComponent;

class CoreComponent extends BaseComponent
{
    protected $coreSettings;

    public function initialize()
    {
        //
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $availableCaches = [];

        foreach ($this->cacheTools->getAvailableCaches() as $key => $value) {
            $availableCaches[$value]['id'] = $value;
            $availableCaches[$value]['name'] = $value;
        }

        $this->view->core = $this->core->core;
        $this->view->availableCaches = $availableCaches;
        $this->view->logLevels = $this->logger->getLogLevels();
        $storage = $this->useStorage('private');

        $storageFiles =
            $this->basepackages->storages->getFiles(
                ['storagetype'  => $storage['permission'],
                 'params'       =>
                    [
                        'conditions'    => 'uuid_location = :uuidLocation: AND storages_id = :storagesId: AND orphan = :orphan:',
                        'bind'          =>
                            [
                                'uuidLocation'    => 'core/',
                                'storagesId'      => $storage['id'],
                                'orphan'          => 0
                            ]
                    ]
                ]
            );

        if ($storageFiles && count($storageFiles) > 0) {
            foreach ($storageFiles as $storageFileKey => &$storageFile) {
                if (strpos($storageFile['org_file_name'], 'db') === false) {
                    unset($storageFiles[$storageFileKey]);
                    continue;
                }
            }
        }

        $this->view->dbStorageFiles = $storageFiles;
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->core->update($this->postData());

            $this->addResponse(
                $this->core->packagesData->responseMessage,
                $this->core->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function resetAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->reset()) {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function dbBackupAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->dbBackup($this->postData())) {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode,
                    $this->core->packagesData->responseData,
                );
            } else {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function dbRestoreAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->dbRestore($this->postData())) {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode,
                    $this->core->packagesData->responseData,
                );
            } else {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}