<?php

namespace Apps\Core\Components\System\Core;

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

        //Unset security keys
        unset($core['settings']['sigKey']);
        unset($core['settings']['sigText']);
        unset($core['settings']['cookiesSig']);

        $this->view->core = $core;
        $this->view->availableCaches = $availableCaches;
        $this->view->logLevels = $this->logger->getLogLevels();
        $storage = $this->useStorage('private');

        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'uuid_location = :uuidLocation: AND storages_id = :storagesId: AND orphan = :orphan:',
                    'bind'          =>
                        [
                            'uuidLocation'    => '.dbbackups/',
                            'storagesId'      => $storage['id'],
                            'orphan'          => 0
                        ]
                ];
        } else {
            $params =
                [
                    'conditions'    =>
                        [
                            ['uuid_location', '=', '.dbbackups/'],
                            ['storages_id', '=', $storage['id']]
                        ]
                ];
        }

        $storageFiles =
            $this->basepackages->storages->getFiles(
                ['storagetype'  => $storage['permission'],
                 'params'       => $params
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

        if ($this->config->databasetype !== 'db') {
            $ffStoresArr = $this->ff->getAllStores();

            $ffStores = [];

            foreach ($ffStoresArr as $ffStore) {
                $ffStores[$ffStore] =
                    [
                        'id'    => $ffStore,
                        'name'  => str_replace('_', ' ', $ffStore)
                    ];
            }

            $this->view->ffStores = $ffStores;
        }
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

    public function maintainDbAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->maintainDb($this->postData())) {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode
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