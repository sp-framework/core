<?php

namespace Apps\Dash\Components\System\Storages;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class StoragesComponent extends BaseComponent
{
    use DynamicTable;

    protected $storages;

    public function initialize()
    {
        $this->storages = $this->usePackage('storages', 'packages');
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['uuid']) && $this->getData()['uuid'] !== '') {
            return $this->storages->getFile($this->getData());
        }

        if ($this->app['id'] == 1) {
            $this->checkSettingsRoute();

            if (isset($this->getData()['id'])) {
                if ($this->getData()['id'] != 0) {
                    $storage = $this->storages->getById($this->getData()['id']);

                    if (!$storage) {
                        return $this->throwIdNotFound();
                    }

                    $storage['allowed_image_mime_types'] = Json::decode($storage['allowed_image_mime_types']);
                    $storage['allowed_image_sizes'] = Json::decode($storage['allowed_image_sizes']);
                    $storage['allowed_file_mime_types'] = Json::decode($storage['allowed_file_mime_types']);

                    $this->view->storage = $storage;

                    $this->view->storageType = $storage['type'];
                } else {
                    $this->view->storageType = $this->getData()['type'];
                }

                $storagePackage = $this->modules->packages->getNamePackage('Storages');

                $storagePackage['settings'] = Json::decode($storagePackage['settings'], true);

                $this->view->allowedImageMimeTypes = $storagePackage['settings']['allowedImageMimeTypes'];
                $this->view->allowedImageSizes = $storagePackage['settings']['allowedImageSizes'];
                $this->view->allowedFileMimeTypes = $storagePackage['settings']['allowedFileMimeTypes'];

                $this->view->responseCode = $this->storages->packagesData->responseCode;

                $this->view->responseMessage = $this->storages->packagesData->responseMessage;

                $this->view->pick('storages/view');

                return;
            }

            $this->view->storageType = '';

            $controlActions =
                [
                    'actionsToEnable'       =>
                    [
                        'edit'      => 'system/storages',
                        'remove'    => 'system/storages/remove'
                    ]
                ];

            $this->generateDTContent(
                $this->storages,
                'system/storages/view',
                null,
                ['name', 'type', 'permission'],
                true,
                ['name', 'type', 'permission'],
                $controlActions,
                null,
                null,
                'name'
            );

            $this->view->pick('storages/list');

            return;
        }

        return false;
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->hasFiles()) {

            if ($this->storages->storeFile()) {
                $this->view->storageData = $this->storages->packagesData->storageData;
            }

            $this->addResponse(
                $this->storages->packagesData->responseMessage,
                $this->storages->packagesData->responseCode
            );

            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->storages->addStorage($this->postData());

            $this->addResponse(
                $this->storages->packagesData->responseMessage,
                $this->storages->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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

            $this->storages->updateStorage($this->postData());

            $this->addResponse(
                $this->storages->packagesData->responseMessage,
                $this->storages->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost() && isset($this->postData()['uuid'])) {

            $this->storages->removeFile($this->postData()['uuid']);

            $this->addResponse(
                $this->storages->packagesData->responseMessage,
                $this->storages->packagesData->responseCode
            );

            return;
        }

        if ($this->app['id'] == '1') {
            if ($this->request->isPost()) {
                if (!$this->checkCSRF()) {
                    return;
                }

                $this->storages->removeStorage($this->postData());

                $this->addResponse(
                    $this->storages->packagesData->responseMessage,
                    $this->storages->packagesData->responseCode
                );
            } else {
                $this->addResponse('Method Not Allowed', 1);
            }
        }
    }
}
