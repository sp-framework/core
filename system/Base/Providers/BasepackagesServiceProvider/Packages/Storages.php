<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

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

    public function addStorage(array $data)
    {
        $data = $this->extractSelectData($data);

        if ($this->add($data)) {
            $this->addResponse('Storage Added');
        } else {
            $this->addResponse('Error Adding Storage', 1);
        }
    }

    public function updateStorage(array $data)
    {
        $data = $this->extractSelectData($data);

        if ($this->update($data)) {
            $this->addResponse('Storage Updated');
        } else {
            $this->addResponse('Error Updating Storage', 1);
        }
    }

    protected function extractSelectData(array $data)
    {
        $data['allowed_image_mime_types'] = $this->helper->decode($data['allowed_image_mime_types'], true);
        $data['allowed_image_mime_types'] = $this->helper->encode($data['allowed_image_mime_types']['data']);
        $data['allowed_image_sizes'] = $this->helper->decode($data['allowed_image_sizes'], true);
        $data['allowed_image_sizes'] = $this->helper->encode($data['allowed_image_sizes']['data']);
        $data['allowed_file_mime_types'] = $this->helper->decode($data['allowed_file_mime_types'], true);
        $data['allowed_file_mime_types'] = $this->helper->encode($data['allowed_file_mime_types']['data']);

        return $data;
    }

    public function removeStorage(array $data)
    {
        if ($data['id'] == '1' || $data['id'] == '2') {
            $this->addResponse('Cannot remove system storages', 1);

            return;
        }

        $storage = $this->getById($data['id']);

        if ($this->remove($data['id'])) {
            $this->addResponse('Storage Removed');
        } else {
            $this->addResponse('Error Removing Storage', 1);
        }
    }

    public function getAppStorages()
    {
        $domain = $this->domains->domain;

        $app = $this->apps->getAppInfo();

        if (isset($domain['apps'][$app['id']]['publicStorage']) && $domain['apps'][$app['id']]['publicStorage'] !== '') {
            $storages['public'] = $this->getById($domain['apps'][$app['id']]['publicStorage']);
        }
        if (isset($domain['apps'][$app['id']]['privateStorage']) && $domain['apps'][$app['id']]['privateStorage'] !== '') {
            $storages['private'] = $this->getById($domain['apps'][$app['id']]['privateStorage']);
        }

        if (count($storages) === 0) {
            return false;
        }

        foreach ($storages as $key => $storage) {
            $storage = $this->jsonData($storage, true);

            $appStorages[$storage['permission']] = $storage;
        }

        return $appStorages;
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

        return $this->initStorage($public)->getFile($getData);
    }

    public function getFiles(array $getData)
    {
        if (!isset($getData['params'])) {
            $this->addResponse('Please provide parameter.', 1);

            return false;
        }

        if (isset($getData['storagetype']) && $getData['storagetype'] === 'public') {
            $public = true;
        } else if (isset($getData['storagetype']) && $getData['storagetype'] === 'private') {
            $public = false;
        } else {
            $public = false;
        }

        return $this->initStorage($public)->getFiles($getData['params']);
    }

    public function getFileById($id, $public = true)
    {
        return $this->initStorage($public)->getById($id);
    }

    public function getFileInfo($uuid, $orgFileName = null, $like = false)
    {
        $fileInfo = $this->initStorage(false)->getFileInfo($uuid, $orgFileName, $like);

        if ($fileInfo && !$like) {
            return $fileInfo[0];
        } else if ($fileInfo && $like) {
            return $fileInfo;
        }

        return false;
    }

    public function storeFile($type = null, $directory = null, $file = null, $fileName = null, $size = null, $mimeType = null, $addToDbOnly = false)
    {
        $this->initStorage($this->checkPublic($type));

        if ($this->storage) {
            if ($this->storage->store($directory, $file, $fileName, $size, $mimeType, $addToDbOnly)) {
                $storageData = $this->storage->packagesData->responseData['storageData'];

                if ($addToDbOnly) {
                    $this->packagesData->responseMessage = 'File pointer added!';
                } else {
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
                }

                $this->packagesData->responseData = $storageData;

                $this->packagesData->responseCode = $this->storage->packagesData->responseCode;

                return true;
            } else {
                $this->addResponse($this->storage->packagesData->responseMessage, $this->storage->packagesData->responseCode);

                return false;
            }
        } else {
            $this->addResponse('Storage not configured, contact administrator', 1);

            return false;
        }
    }

    public function removeFile(string $uuid, $type = null, $purge = null)
    {
        $this->initStorage($this->checkPublic($type));

        if ($this->storage->removeFile($uuid, $this->checkPurge($purge))) {
            $this->addResponse($this->storage->packagesData->responseMessage, $this->storage->packagesData->responseCode);

            return true;
        } else {
            $this->addResponse($this->storage->packagesData->responseMessage, $this->storage->packagesData->responseCode);

            return false;
        }
    }

    protected function initStorage($public = true)
    {
        $domain = $this->domains->domain;

        $app = $this->apps->getAppInfo();

        if (!$domain) {
            $domain = $this->domains->getById(1);

            if (!is_array($domain['apps']) && $domain['apps'] !== '') {
                $domain['apps'] = $this->helper->decode($domain['apps'], true);
            }
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

    protected function checkPublic($type = null)
    {
        $public = true;

        if (isset($this->request->getPost()['storagetype'])) {
            if ($this->request->getPost()['storagetype'] === 'private') {
                $public = false;
            }
        } else if ($type) {
            if ($type === 'private') {
                $public = false;
            }
        }

        return $public;
    }

    protected function checkPurge($purge = null)
    {
        $shouldPurge = false;

        if (isset($this->request->getPost()['purge'])) {
            if ($this->request->getPost()['purge'] == 'true') {
                $shouldPurge = true;
            }
        } else if ($purge !== null) {
            if ($purge === true) {
                $shouldPurge = true;
            }
        }

        return $shouldPurge;
    }

    public function getPublicLink($uuid, $width = null)
    {
        return $this->initStorage()->getPublicLink($uuid, $width);
    }

    public function changeOrphanStatus(string $newUUID = null, string $oldUUID = null, bool $array = false, $status = null, $orgFileName = null, $like = false)
    {
        return $this->initStorage()->changeOrphanStatus($newUUID, $oldUUID, $array, $status, $orgFileName, $like);
    }
}