<?php

namespace Apps\Core\Components\System\Tools\Backuprestore;

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

        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'uuid_location = :uuidLocation: AND storages_id = :storagesId: AND orphan = :orphan:',
                    'bind'          =>
                        [
                            'uuidLocation'    => '.backups/',
                            'storagesId'      => $storage['id'],
                            'orphan'          => 0
                        ]
                ];
        } else {
            $params =
                [
                    'conditions'    => [['uuid_location', '=', '.backups/'], ['storages_id', '=', $storage['id']], ['orphan', '=', 0]]
                ];
        }

        $storageFiles =
            $this->basepackages->storages->getFiles(
                [
                    'storagetype'  => $storage['permission'],
                    'params'       => $params
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
        $this->requestIsPost();

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
    }

    public function restoreAction()
    {
        $this->requestIsPost();

        $this->basepackages->backuprestore->init('restore')->restore($this->postData());

        $this->addResponse(
            $this->basepackages->backuprestore->packagesData->responseMessage,
            $this->basepackages->backuprestore->packagesData->responseCode
        );
    }
}