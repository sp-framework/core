<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

use Phalcon\Helper\Json;
use Phalcon\Image\Adapter\Imagick;
use Phalcon\Image\Enum;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages\BasepackagesStoragesLocal;
use System\Base\Providers\ContentServiceProvider\Local\Content;

class Local extends BasePackage
{
    protected $modelToUse = BasepackagesStoragesLocal::class;

    protected $packageName = 'local';

    public $storage;

    protected $settingsImagesPath;

    protected $settingsCachePath;

    protected $settingsDataPath;

    protected $storagePath;

    protected $imageStorage;

    protected $fileStorage;

    protected $file;

    protected $fileName;

    protected $directory;

    protected $sizes;

    protected $upload;

    protected $mimeType;

    protected $uuid;

    protected $getData;

    protected $width;

    public function initLocal(array $storage)
    {
        $this->storage = $storage;

        $this->settingsImagesPath =
            isset($this->storage['images_path']) ?
            $this->storage['images_path'] :
            'images';

        $this->settingsCachePath =
            isset($this->storage['cache_path']) ?
            $this->storage['cache_path'] :
            'cache';

        $this->settingsDataPath =
            isset($this->storage['data_path']) ?
            $this->storage['data_path'] :
            'data';

        $this->settingsDefaultImageQuality =
            isset($this->storage['default_image_quality']) ?
            $this->storage['default_image_quality'] :
            90;

        $this->storagePath = base_path($this->storage['permission'] . '/' . $this->storage['id']);

        $this->imagesPath = $this->storagePath . '/' . $this->settingsImagesPath . '/';

        $this->cachePath = $this->storagePath . '/' . $this->settingsCachePath . '/';

        $this->dataPath = $this->storagePath . '/' . $this->settingsDataPath . '/';

        $this->storage['allowed_image_mime_types'] =
            isset($this->storage['allowed_image_mime_types']) ?
            Json::decode($this->storage['allowed_image_mime_types']) :
            [];

        $this->imageMimeTypes = $this->storage['allowed_image_mime_types'];

        $this->storage['allowed_image_sizes'] =
            isset($this->storage['allowed_image_sizes']) ?
            Json::decode($this->storage['allowed_image_sizes']) :
            [30, 80, 200, 800, 1200, 2000];

        $this->allowedImageSizes = $this->storage['allowed_image_sizes'];

        $this->storage['allowed_file_mime_types'] =
            isset($this->storage['allowed_file_mime_types']) ?
            Json::decode($this->storage['allowed_file_mime_types']) :
            [];

        $this->fileMimeTypes = $this->storage['allowed_file_mime_types'];

        $this->imageStorage =
            (new Content())->init(
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsImagesPath . '/',
                ['visibility' => $this->storage['permission']]
            );

        $this->fileStorage =
            (new Content())->init(
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsDataPath . '/',
                ['visibility' => $this->storage['permission']]
            );

        return $this;
    }

    public function store()
    {
        if (!$this->request->hasFiles()) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'File(s) Not Provided';

            return false;
        }

        $this->directory = $this->request->getPost()['directory'] ?? null;

        $storageData = [];

        foreach ($this->request->getUploadedFiles() as $key => $file) {

            $this->file = $file;

            $this->fileName =
                isset($this->request->getPost()['fileName']) ?
                $this->request->getPost()['fileName'] :
                $file->getName();

            $this->mimeType = $file->getRealType();

            $this->generateUUID();

            if (in_array($this->mimeType, $this->imageMimeTypes)) {
                if (isset($this->storage['max_image_file_size']) &&
                    $file->getSize() > $this->storage['max_image_file_size']
                ) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'File ' . $this->fileName . ' exceeds allowed file size.';

                    return false;
                }

                $this->storeImage();

            } else if (in_array($this->mimeType, $this->fileMimeTypes)) {
                if (isset($this->storage['max_data_file_size']) &&
                    $file->getSize() > $this->storage['max_data_file_size']
                ) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'File ' . $this->fileName . ' exceeds allowed file size.';

                    return false;
                }

                $this->storeFile();

            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'File Type Not Accepted';

                return false;
            }

            $storageData['uuid'] = $this->uuid;
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->storageData = $storageData;

        $this->packagesData->responseMessage = 'File(s) Uploaded';

        return true;
    }

    protected function storeImage()
    {
        if ($this->directory && !is_dir($this->imagesPath . $this->directory)) {
            $this->imageStorage->createDirectory($this->directory);
        }

        $this->moveImageToLocationAsUUID();

        $this->addFileInfoToDb();
    }

    protected function storeFile()
    {
        if ($this->directory && !is_dir($this->dataPath . $this->directory)) {
            $this->fileStorage->createDirectory($this->directory);
        }

        $this->moveFileToLocationAsUUID();

        $this->addFileInfoToDb();
    }

    protected function generateUUID()
    {
        $this->uuid = $this->random->uuid();

        $this->uuidLocation = $this->directory . '/' . $this->uuid;
    }

    protected function moveImageToLocationAsUUID()
    {
        $this->file->moveTo($this->imagesPath . $this->uuidLocation);
    }

    protected function moveFileToLocationAsUUID()
    {
        $this->file->moveTo($this->dataPath . $this->uuidLocation);
    }

    protected function addFileInfoToDb()
    {
        $data =
            [
                'storages_id'           => $this->storage['id'],
                'uuid'                  => $this->uuid,
                'uuid_location'         => $this->directory . '/',
                'org_file_name'         => $this->fileName,
                'type'                  => $this->mimeType,
                'orphan'                => 1,
                'created_by'            => $this->auth->account()['id'] ?? 0,
                'updated_by'            => $this->auth->account()['id'] ?? 0
            ];

        $this->add($data);
    }

    public function get(array $getData)
    {
        $this->getData = $getData;

        $file = $this->getFileInfo($this->getData['uuid']);
        if (!$file) {
            return $this->response->setStatusCode(404, 'Not Found');
        }

        if (in_array($file[0]['type'], $this->imageMimeTypes)) {
            if (isset($this->getData['w']) && in_array($this->getData['w'], $this->allowedImageSizes)) {
                $sizedImage = $this->getSizedImage($file[0], $this->getData['w']);

                if ($sizedImage) {
                    $this->response->setContentType($file[0]['type']);

                    $this->response->setContent($this->localContent->read($sizedImage));
                }

                return $this->response->send();
            } else {
                $this->response->setStatusCode(404, 'Not Found');

                return $this->response->send();
            }
        } else if (in_array($file[0]['type'], $this->fileMimeTypes)) {
            $dataFile =
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsDataPath . '/' . $file[0]['uuid_location'] . $file[0]['uuid'];

            $this->updateFileLink(
                $file[0],
                null,
                '/' . $this->storage['id'] . '/' . $this->settingsDataPath . '/' . $file[0]['uuid_location'] . $file[0]['uuid']
            );

            if (isset($this->request->getPost()['getpubliclinks'])) {
                return;
            }

            $this->response->setContentType($file[0]['type']);

            $this->response->setHeader("Content-Length", filesize(base_path($dataFile)));

            return $this->response->setContent($this->localContent->read($dataFile));

        } else {
            $this->response->setStatusCode(404, 'Not Found');

            return $this->response->send();
        }
    }

    public function getFileInfo($uuid)
    {
        return $this->getByParams(
            [
                'conditions'    => 'uuid = :uuid:',
                'bind'          =>
                    [
                        'uuid'    => $uuid
                    ]
            ]);
    }

    protected function getSizedImage($file, $width)
    {
        $imageFile = '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsImagesPath . '/' . $file['uuid_location'] . $file['uuid'];

        if (!$this->localContent->fileExists($imageFile)) {

            $this->logger->log->info('File with UUID is in database, but not at location ' . $imageFile);

            $this->response->setStatusCode(404, 'Not Found');

            return false;
        }

        $image = new Imagick(base_path($imageFile));

        // If max width of image is less than requested size, make width to image size.
        if ($image->getWidth() < $width) {
            $this->width = $image->getWidth();
        } else {
            $this->width = $width;
        }

        if ($image->getMime() === 'image/PNG') {
            $imageFormat = '.png';
        } else if ($image->getMime() === 'image/JPEG') {
            $imageFormat = '.jpg';
        } else if ($image->getMime() === 'image/GIF') {
            $imageFormat = '.gif';
        }

        $sizedImage =
            '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsCachePath . '/' . $file['uuid_location'] . $file['uuid'] . '/' . $this->width . $imageFormat;

        if ($this->localContent->fileExists($sizedImage) && !isset($this->getData['quality'])) {

            return $sizedImage;

        } else {
            $image->resize($this->width, null, Enum::WIDTH);

            //Put empty content that will be overridden on image save
            $this->localContent->write($sizedImage, '');

            if (isset($this->getData['quality'])) {
                $this->settingsDefaultImageQuality = $this->getData['quality'];
            }

            $image->save($this->cachePath . $file['uuid_location'] . $file['uuid'] . '/' . $this->width . $imageFormat, $this->settingsDefaultImageQuality);

            //Only update links for public as users cannot access private folder.
            if ($this->storage['permission'] === 'public') {
                $this->updateFileLink(
                    $file,
                    $this->width,
                    '/' . $this->storage['id'] . '/' . $this->settingsCachePath . '/' . $file['uuid_location'] . $file['uuid'] . '/' . $this->width . $imageFormat
                );
            }

            return $sizedImage;
        }
    }

    public function getPublicLink(string $uuid, $width = null)
    {
        $file = $this->getFileInfo($uuid);

        if (!$file) {
            return '#';
        }

        if (isset($file[0]['links'])) {
            $file[0]['links'] = Json::decode($file[0]['links'], true);
        }

        if ($width) {
            if (isset($file[0]['links'][$width])) {
                if (isset($this->request->getPost()['getpubliclinks'])) {
                    return [$width => $file[0]['links'][$width]];
                } else {
                    return $file[0]['links'][$width];
                }
            }

            if ($this->width) {
                if (in_array($this->width, $this->allowedImageSizes)) {
                    $this->getSizedImage($file[0], $this->width);
                } else {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Requested Width not registered with system.';

                    return false;
                }
            } else {
                if (in_array($width, $this->allowedImageSizes)) {
                    $this->getSizedImage($file[0], $width);
                } else {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Requested Width not registered with system.';

                    return false;
                }
            }

            return $this->getPublicLink($uuid, $this->width);

        } else {
            if (isset($file[0]['links']['data'])) {
                return $file[0]['links']['data'];
            }

            $this->get(['uuid' => $uuid]);

            return $this->getPublicLink($uuid);
        }
    }

    protected function updateFileLink($file, $width = null, $link)
    {
        if ($file['links'] && !is_array($file['links'])) {
            $file['links'] = Json::decode($file['links'], true);
        }

        if ($width) {
            $file['links'][$width] = $link;
        } else {
            $file['links']['data'] = $link;
        }
        $file['links'] = Json::encode($file['links']);

        $this->update($file);
    }

    public function removeFile($uuid, $purge = false)
    {
        if (!$uuid) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Please provide UUID';

            return false;
        }

        if ($purge) {
            return $this->purgeFile($uuid);
        } else {
            return $this->flipOrphanStatus($uuid, 1);
        }
    }

    protected function purgeFile($uuid)
    {
        $file = $this->getFileInfo($uuid);

        if ($file && count($file) === 1) {

            if ($file[0]['uuid_location'] && $file[0]['uuid_location'] !== '') {
                $fileLocation = $file[0]['uuid_location'];
                $fileLocation = trim($fileLocation, '/');
                $fileLocation = '/' . $fileLocation . '/';
            } else {
                $fileLocation = '';
            }
            if (in_array($file[0]['type'], $this->imageMimeTypes)) {
                $fileRemovedFromDB = $this->removeFileFromDb($file[0]['id']);

                $fileDeleted =
                    $this->removeFileFromLocation(
                        '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsImagesPath . $fileLocation . $file[0]['uuid']
                    );

                $fileCacheDeleted =
                    $this->removeFileCache(
                        '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsCachePath . $fileLocation . $file[0]['uuid']
                    );

                if (!$fileRemovedFromDB) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error deleting file from DB';

                    return true;
                }

                if (!$fileDeleted) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error deleting file from location';

                    return true;
                }

                if (!$fileCacheDeleted) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error deleting file from cache';

                    return true;
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'File purged from DB, Location and cache';

                return true;

            } else if (in_array($file[0]['type'], $this->fileMimeTypes)) {
                $fileRemovedFromDB = $this->removeFileFromDb($file[0]['id']);

                $fileDeleted = $this->removeFileFromLocation(
                    '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsDataPath . $fileLocation . $file[0]['uuid']
                );

                if (!$fileRemovedFromDB) {
                    $this->packagesData->responseCode = 1;
                    $this->packagesData->responseMessage = 'Error deleting file from DB';
                    return true;
                }

                if (!$fileDeleted) {
                    $this->packagesData->responseCode = 1;
                    $this->packagesData->responseMessage = 'Error deleting file from location';
                    return true;
                }

                $this->packagesData->responseCode = 0;
                $this->packagesData->responseMessage = 'File removed from DB, Location and cache';
                return true;
            }
        } else if ($file && count($file) === 0) {
            $fileImageDeleted = $this->removeFileFromLocation(
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsImagesPath . $fileLocation . $file[0]['uuid']
            );
            $fileDataDeleted = $this->removeFileFromLocation(
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsDataPath . $fileLocation . $file[0]['uuid']
            );
            $fileCacheDeleted = $this->removeFileCache(
                '/' . $this->storage['permission'] . '/' . $this->storage['id'] . '/' . $this->settingsCachePath . $fileLocation . $file[0]['uuid']
            );

            if ($fileImageDeleted || $fileDataDeleted || $fileCacheDeleted) {
                $this->packagesData->responseCode = 0;
                $this->packagesData->responseMessage = 'File not found in DB, but in location/cache. Files removed from location/cache.';
                return true;
            }

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Incorrect UUID, Not in DB and/or location/cache.';

            return true;
        }
        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Incorrect UUID, Not in DB and/or location/cache.';

        return true;
    }

    protected function removeFileFromDb($id)
    {
        if ($this->remove($id)) {
            return true;
        }
    }

    protected function removeFileFromLocation($location)
    {
        if ($this->localContent->fileExists($location)) {
            $fileDeleted = $this->localContent->delete($location);
        } else {
            $fileDeleted = false;
        }

        $this->recursiveDeleteEmptyFolders($location);

        return $fileDeleted;
    }

    protected function removeFileCache($location)
    {
        if ($this->localContent->fileExists($location)) {
            $cacheFiles = $this->localContent->listContents($location, true);
            if ($cacheFiles && count($cacheFiles) > 0) {
                foreach ($cacheFiles as $key => $file) {
                    if (!$this->localContent->delete($file['path'])) {
                        $fileCacheDeleted = false;
                    } else {
                        $fileCacheDeleted = true;
                    }
                }
            }
        } else {
            $fileCacheDeleted = true;
        }

        $this->recursiveDeleteEmptyFolders($location, true);

        return $fileCacheDeleted;
    }

    protected function recursiveDeleteEmptyFolders($location, $cache = false)
    {
        $locationArr = explode('/', trim($location, '/'));

        if (!$cache) {
            unset($locationArr[count($locationArr) - 1]); //Get rid of the UUID
        }
        $paths = [];

        for ($i = 0; $i <= count($locationArr) - 1; $i++) {
            $path = '';
            for ($j = 0; $j <= $i; $j++) {
                $path .= $locationArr[$j] . '/';
            }
            array_push($paths, $path);
        }

        for ($checkPath = count($paths) - 1; $checkPath >= 3; $checkPath--) { //>=3 to ignore images/, cache/, data/, storageID directory
            if (count($this->localContent->listContents($paths[$checkPath], false)) === 0) {
                $this->localContent->deleteDir($paths[$checkPath]);
            }
        }
    }

    public function changeOrphanStatus(string $newUUID = null, string $oldUUID = null, bool $array = false)
    {
        if ($array) {
            if ($oldUUID) {
                $olduuids = Json::decode($oldUUID, true);

                foreach ($olduuids as $olduuidKey => $olduuid) {
                    $this->flipOrphanStatus($olduuid, 1);
                }
            }

            if ($newUUID) {
                $uuids = Json::decode($newUUID, true);
                foreach ($uuids as $uuidKey => $newuuid) {
                    $this->flipOrphanStatus($newuuid, 0);
                }
            }

        } else {
            if ($oldUUID) {
                $this->flipOrphanStatus($oldUUID, 1);
            }

            if ($newUUID) {
                $this->flipOrphanStatus($newUUID, 0);
            }
        }
    }

    protected function flipOrphanStatus($uuid, $status)
    {
        $file = $this->getFileInfo($uuid);

        if ($file && count($file) === 1) {
            $file[0]['orphan'] = $status;

            if ($this->update($file[0])) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'UUID is now marked orphan';

                return true;
            }
        }

        return false;
    }

    public function clearOrphans()
    {
        // @todo orphan also needs to search whole DB for uploads_data and compare with files table.
        // We need to make a backup of orphans and keep them zipped in a directory. Take database snapshot as json in it. This way we can recover it if something important is lost.
        $dataDirContents = $this->fileStorage->listContents('/data', true);

        $dataDirFiles = [];

        foreach ($dataDirContents as $dataDirContentsKey => $dataDirContentsValue) {
            if ($dataDirContentsValue['type'] === 'file') {
                array_push($dataDirFiles, $dataDirContentsValue);
            }
        }

        $dbDataDirFiles = [];

        $dbDataDirContents = $this->db->getRepository(StorageFiles::class)->findAll();

        foreach ($dbDataDirContents as $dbDataDirContentsKey => $dbDataDirContentsValue) {
            $objToArr = $dbDataDirContentsValue->getAllArr();
            array_push($dbDataDirFiles, $objToArr['uuid']);
        }

        $count = 0;

        foreach ($dataDirFiles as $file) {
            if (!in_array($file['filename'], $dbDataDirFiles)) {
                $file['path'] = str_replace("data/", "/", $file['path']);
                $count = $count + 1;
                $this->fileStorage->remove('data' . $file['path']);
                $this->fileStorage->removeDir('cache' . $file['path']);
                $this->recursiveDeleteEmptyFolders('data' . $file['path']);
                $this->recursiveDeleteEmptyFolders('cache' . $file['path']);
            }
        }

        if ($count > 0) {
            $this->packagesData->responseMessage = $count . ' file(s) removed from location and cache';
        } else if ($count === 0) {
            $this->packagesData->responseMessage = 'No orphan files found!';
        }
        $this->packagesData->responseCode = 0;

        return $this->view->render($this->response, '', $this->templateData);
    }

    public function clearCache()
    {
        //@todo move this to settings
        //@todo - add method to check all image uuids in db and clean files db
        $twigCache = $this->twigFileStorage->listContents('/', true);
        $cacheDirContents = $this->fileStorage->listContents('/cache', true);

        if (count($twigCache) > 0) {
            foreach ($twigCache as $twigCacheKey => $twigCacheValue) {
                if ($twigCacheValue['type'] === 'dir') {
                    $this->twigFileStorage->removeDir($twigCacheValue['path']);
                }
            }
        }
        if (count($cacheDirContents) > 0) {
            foreach ($cacheDirContents as $cacheDirKey => $cacheDirValue) {
                if ($cacheDirValue['type'] === 'dir') {
                    $this->fileStorage->removeDir($cacheDirValue['path']);
                }
            }
        }


        $this->packagesData->responseMessage = 'Cleared HTML & Image Cache!';
        $this->packagesData->responseCode = 0;

        return $this->view->render($this->response, '', $this->templateData);
    }
}