<?php

namespace Applications\Ecom\Admin\Components\Storages;

use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class StoragesComponent extends BaseComponent
{
    use DynamicTable;

    protected $storages;

    public function initialize()
    {
        $this->storages = $this->basepackages->storages;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['uuid']) && $this->getData()['uuid'] !== '') {
            return $this->storages->getFile($this->getData());
            // var_dump($this->storages->getPublicLink('f8c7c7ef-1ab6-4236-8582-36d9ebaa86f9'));
            // die();
        }

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $storage = $this->storages->getById($this->getData()['id']);

                $storage['allowed_image_mime_types'] = Json::decode($storage['allowed_image_mime_types']);
                $storage['allowed_image_sizes'] = Json::decode($storage['allowed_image_sizes']);
                $storage['allowed_file_mime_types'] = Json::decode($storage['allowed_file_mime_types']);

                $this->view->storage = $storage;

                $this->view->storageType = $storage['type'];
            } else {
                $this->view->storageType = $this->getData()['type'];
            }

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

            $this->view->pick('storages/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'storages',
                    'remove'    => 'storages/remove'
                ]
            ];

        $this->generateDTContent(
            $this->storages,
            'storages/view',
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

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->storages->addStorage($this->postData());

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost() && isset($this->postData()['uuid'])) {

            $this->storages->removeFile($this->postData()['uuid']);

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

            return;
        }

        if ($this->request->isPost()) {

            $this->storages->removeStorage($this->postData());

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}