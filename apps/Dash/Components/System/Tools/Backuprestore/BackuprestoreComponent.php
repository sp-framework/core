<?php

namespace Apps\Dash\Components\System\Tools\Backuprestore;

use System\Base\BaseComponent;

class BackuprestoreComponent extends BaseComponent
{
    public function initialize()
    {
        //
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->getNewToken();

        if (isset($this->getData()['analyse']) && $this->getData()['analyse'] == 'info') {
            $backupInfoFile = $this->basepackages->backuprestore->analyseBackinfoFile($this->getData()['id']);

            if ($backupInfoFile) {
                return $this->view->getPartial('backuprestore/analyse/analysis', ['backupInfoFile' => $backupInfoFile]);
            }

            return $this->basepackages->backuprestore->packagesData->responseMessage;
        }

        $storage = $this->useStorage('private');

        $storageFiles =
            $this->basepackages->storages->getFiles(
                ['storagetype'  => $storage['permission'],
                 'params'       =>
                    [
                        'conditions'    => 'uuid_location = :uuidLocation: AND storages_id = :storagesId: AND orphan = :orphan:',
                        'bind'          =>
                            [
                                'uuidLocation'    => '.backups/',
                                'storagesId'      => $storage['id'],
                                'orphan'          => 0
                            ]
                    ]
                ]
            );

        if ($storageFiles && count($storageFiles) > 0) {
            foreach ($storageFiles as $storageFileKey => &$storageFile) {
                if (strpos($storageFile['org_file_name'], 'backup-') === false) {
                    unset($storageFiles[$storageFileKey]);
                    continue;
                }
            }
        }

        $this->view->dbStorageFiles = $storageFiles;
    }

    public function backupAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->basepackages->backuprestore->init()->backup($this->postData())) {
                $this->addResponse(
                    $this->basepackages->backuprestore->packagesData->responseMessage,
                    $this->basepackages->backuprestore->packagesData->responseCode,
                    $this->basepackages->backuprestore->packagesData->responseData,
                );
            } else {
                $this->addResponse(
                    $this->basepackages->backuprestore->packagesData->responseMessage,
                    $this->basepackages->backuprestore->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function restoreAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->basepackages->backuprestore->init()->restore($this->postData())) {
                $this->addResponse(
                    $this->basepackages->backuprestore->packagesData->responseMessage,
                    $this->basepackages->backuprestore->packagesData->responseCode,
                    $this->basepackages->backuprestore->packagesData->responseData,
                );
            } else {
                $this->addResponse(
                    $this->basepackages->backuprestore->packagesData->responseMessage,
                    $this->basepackages->backuprestore->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}