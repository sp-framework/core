<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Applications\Ecom\Admin\Packages\Channels\Channels;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages as StoragesModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages\Local;

class Storages extends BasePackage
{
    protected $modelToUse = StoragesModel::class;

    public $storages;

    public $storage;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function addStorage(array $data)
    {
        $data = $this->extractSelectData($data);

        $add = $this->add($data);

        if ($add) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Storage Added';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Adding Storage';
        }
    }

    public function updateStorage(array $data)
    {
        $data = $this->extractSelectData($data);

        $update = $this->update($data);

        if ($update) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Storage Updated';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Updating Storage';
        }
    }

    protected function extractSelectData(array $data)
    {
        $data['allowed_image_mime_types'] = Json::decode($data['allowed_image_mime_types'], true);
        $data['allowed_image_mime_types'] = Json::encode($data['allowed_image_mime_types']['data']);
        $data['allowed_image_sizes'] = Json::decode($data['allowed_image_sizes'], true);
        $data['allowed_image_sizes'] = Json::encode($data['allowed_image_sizes']['data']);
        $data['allowed_file_mime_types'] = Json::decode($data['allowed_file_mime_types'], true);
        $data['allowed_file_mime_types'] = Json::encode($data['allowed_file_mime_types']['data']);

        return $data;
    }

    public function removeStorage(array $data)
    {
        $remove = $this->remove($data['id']);

        if ($remove) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Storage Removed';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Removing Storage';
        }
    }

    public function getFile(array $getData)
    {
        if ($getData['storagetype'] === 'public') {
            $public = true;
        } else if ($getData['storagetype'] === 'private') {
            $public = false;
        }
        return $this->initStorage($public)->get($getData);
    }

    public function storeFile()
    {
        if ($this->request->getPost()['storagetype'] === 'public') {
            $public = true;
        } else if ($this->request->getPost()['storagetype'] === 'private') {
            $public = false;
        }
        $storage = $this->initStorage($public);

        if ($storage->store()) {
            $this->packagesData->storageData = $storage->packagesData->storageData;

            $this->packagesData->responseCode = $storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $storage->packagesData->responseMessage;

            return true;
        } else {
            $this->packagesData->responseCode = $storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $storage->packagesData->responseMessage;

            return false;
        }
    }

    public function removeFile(string $uuid)
    {
        if ($this->request->getPost()['storagetype'] === 'public') {
            $public = true;
        } else if ($this->request->getPost()['storagetype'] === 'private') {
            $public = false;
        }
        $storage = $this->initStorage($public);

        if ($storage->removeFile($uuid)) {
            $this->packagesData->responseCode = $storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $storage->packagesData->responseMessage;

            return true;
        } else {
            $this->packagesData->responseCode = $storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $storage->packagesData->responseMessage;

            return false;
        }
    }

    protected function initStorage($public = true)
    {
        if (isset($this->request->getPost()['channel']) && $this->request->getPost()['channel'] !== '') {
            $channels = $this->usePackage(Channels::class);

            $channel = $channels->getById($this->request->getPost()['channel']);

            $channel['settings'] = Json::decode($channel['settings'], true);

            $domain = $this->basepackages->domains->getById($channel['settings']['domain_id']);
            $domain['applications'] = Json::decode($domain['applications'], true);

            $application = $this->modules->applications->getById($channel['settings']['application_id']);

        } else {
            $domain = $this->basepackages->domains->domain;

            $application = $this->modules->applications->getApplicationInfo();
        }
        if ((isset($domain['applications'][$application['id']]['publicStorage']) &&
            $domain['applications'][$application['id']]['publicStorage'] !== '') &&
            (isset($domain['applications'][$application['id']]['privateStorage']) &&
            $domain['applications'][$application['id']]['privateStorage'] !== '')
        ) {
            if ($public) {
                $storage = $this->getById($domain['applications'][$application['id']]['publicStorage']);
            } else {
                $storage = $this->getById($domain['applications'][$application['id']]['privateStorage']);
            }
        } else {
            return false;
        }

        if ($storage['type'] === 'local') {
            $this->storage = (new Local())->initLocal($storage);
        }

        return $this->storage;
    }

    public function getPublicLink($uuid, $width = null)
    {
        var_dump($this->initStorage());
        return $this->initStorage()->getPublicLink($uuid, $width);
    }
}