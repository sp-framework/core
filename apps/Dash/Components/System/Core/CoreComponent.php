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
            if ($value === 'streamCache') {
                $availableCaches[$value]['name'] = 'Local Files';
            } else if ($value === 'apcuCache') {
                $availableCaches[$value]['name'] = 'APCu Cache';
            } else if ($value === 'memCached') {
                $availableCaches[$value]['name'] = 'MemCached';
            } else if ($value === 'redis') {
                $availableCaches[$value]['name'] = 'Redis';
            }
        }

        $core = $this->core->core;

        if (isset($core['settings']['logs']['emergencyLogsEmailAddresses']) &&
            is_array($core['settings']['logs']['emergencyLogsEmailAddresses'])
        ) {
            $core['settings']['logs']['emergencyLogsEmailAddresses'] = trim(implode(', ', $core['settings']['logs']['emergencyLogsEmailAddresses']));
        }

        $this->view->core = $core;
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

            $this->core->updateCore($this->postData());

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

    public function removeDbAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->removeDb($this->postData())) {
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

    public function updateDbAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->updateDb($this->postData())) {
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

    public function checkPwStrengthAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->checkPwStrength($this->postData()['pass']) !== false) {
                $this->view->responseData = $this->core->packagesData->responseData;
            }

            $this->addResponse(
                $this->core->packagesData->responseMessage,
                $this->core->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function generatePwAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->core->generateNewPassword();

            $this->addResponse(
                $this->core->packagesData->responseMessage,
                $this->core->packagesData->responseCode,
                $this->core->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}