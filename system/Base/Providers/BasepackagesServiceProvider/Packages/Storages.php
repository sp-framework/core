<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Apps\Ecom\Admin\Packages\Channels\Channels;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesStorages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages\Local;

class Storages extends BasePackage
{
    protected $modelToUse = BasepackagesStorages::class;

    public $storages;

    public $storage;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
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

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
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

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
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

    public function getAppStorages()
    {
        if (isset($this->storages) && count($this->storages) > 0) {
            foreach ($this->storages as $key => $storage) {
                if ($storage['allowed_image_mime_types']) {
                    $storage['allowed_image_mime_types'] = Json::decode($storage['allowed_image_mime_types']);
                }
                if ($storage['allowed_image_sizes']) {
                    $storage['allowed_image_sizes'] = Json::decode($storage['allowed_image_sizes']);
                }
                if ($storage['allowed_file_mime_types']) {
                    $storage['allowed_file_mime_types'] = Json::decode($storage['allowed_file_mime_types']);
                }
                $storages[$storage['permission']] = $storage;
            }

            return $storages;
        }

        return false;
    }

    public function getFile(array $getData)
    {
        if (isset($getData['storagetype']) && $getData['storagetype'] === 'public') {
            $public = true;
        } else if (isset($getData['storagetype']) && $getData['storagetype'] === 'private') {
            $public = false;
        } else {
            $public = false;
        }

        return $this->initStorage($public)->get($getData);
    }

    public function getFileInfo($uuid)
    {
        $fileInfo = $this->initStorage(false)->getFileInfo($uuid);

        if ($fileInfo) {
            return $fileInfo[0];
        }
        return false;
    }

    public function storeFile()
    {
        if (isset($this->request->getPost()['storagetype'])) {
            if ($this->request->getPost()['storagetype'] === 'public') {
                $public = true;
            } else if ($this->request->getPost()['storagetype'] === 'private') {
                $public = false;
            }
        } else {
            $public = true;
        }

        $this->initStorage($public);

        if ($this->storage) {
            if ($this->storage->store()) {
                $storageData = $this->storage->packagesData->storageData;

                $fileInfo = $this->storage->getFileInfo($storageData['uuid']);

                if (isset($fileInfo[0])) {
                    $fileType = $fileInfo[0]['type'];
                }

                if (in_array($fileType, $this->storage->storage['allowed_image_mime_types'])) {
                    if (isset($this->request->getPost()['getpubliclinks'])) {
                        $widths = explode(',', $this->request->getPost()['getpubliclinks']);

                        $storageData['publicLinks'] = [];

                        foreach ($widths as $width) {
                            $width = trim($width);

                            array_push($storageData['publicLinks'], $this->getPublicLink($storageData['uuid'], (int) $width));
                        }
                    }

                    $this->packagesData->responseMessage = 'Files Uploaded!';

                } else if (in_array($fileType, $this->storage->storage['allowed_file_mime_types'])) {
                    if (isset($this->request->getPost()['getpubliclinks'])) {
                        $storageData['publicLinks'] = [];

                        array_push($storageData['publicLinks'], $this->getPublicLink($storageData['uuid'], null));
                    }

                    $this->packagesData->responseMessage = 'Files Uploaded!';
                }

                $this->packagesData->storageData = $storageData;

                $this->packagesData->responseCode = $this->storage->packagesData->responseCode;

                return true;
            } else {
                $this->packagesData->responseCode = $this->storage->packagesData->responseCode;

                $this->packagesData->responseMessage = $this->storage->packagesData->responseMessage;

                return false;
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Storage not configured, contact administrator';

            return false;
        }
    }

    public function removeFile(string $uuid)
    {
        if (isset($this->request->getPost()['storagetype'])) {
            if ($this->request->getPost()['storagetype'] === 'public') {
                $public = true;
            } else if ($this->request->getPost()['storagetype'] === 'private') {
                $public = false;
            }
        } else {
            $public = true;
        }

        $this->initStorage($public);

        if (isset($this->request->getPost()['purge'])) {
            if ($this->request->getPost()['purge'] == 'true') {
                $purge = true;
            } else {
                $purge = true;
            }
        } else {
            $purge = false;
        }

        if ($this->storage->removeFile($uuid, $purge)) {
            $this->packagesData->responseCode = $this->storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $this->storage->packagesData->responseMessage;

            return true;
        } else {
            $this->packagesData->responseCode = $this->storage->packagesData->responseCode;

            $this->packagesData->responseMessage = $this->storage->packagesData->responseMessage;

            return false;
        }
    }

    protected function initStorage($public = true)
    {
        if (isset($this->request->getPost()['channel']) && $this->request->getPost()['channel'] !== '') {
            $channels = $this->usePackage(Channels::class);

            $channel = $channels->getById($this->request->getPost()['channel']);

            $channel['settings'] = Json::decode($channel['settings'], true);

            $domain = $this->domains->getById($channel['settings']['domain_id']);
            $domain['apps'] = Json::decode($domain['apps'], true);

            $app = $this->apps->getById($channel['settings']['app_id']);

        } else {
            $domain = $this->domains->domain;

            $app = $this->apps->getAppInfo();
        }

        if ((isset($domain['apps'][$app['id']]['publicStorage']) &&
            $domain['apps'][$app['id']]['publicStorage'] !== '') &&
            (isset($domain['apps'][$app['id']]['privateStorage']) &&
            $domain['apps'][$app['id']]['privateStorage'] !== '')
        ) {
            if ($public) {
                $storage = $this->getById($domain['apps'][$app['id']]['publicStorage']);
            } else {
                $storage = $this->getById($domain['apps'][$app['id']]['privateStorage']);
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
        return $this->initStorage()->getPublicLink($uuid, $width);
    }

    public function changeOrphanStatus(string $newUUID = null, string $oldUUID = null, bool $array = false)
    {
        return $this->initStorage()->changeOrphanStatus($newUUID, $oldUUID, $array);
    }
}