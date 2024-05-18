<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Storages;

class Storages
{
    protected $db;

    protected $ff;

    protected $helper;

    public function register($db, $ff, $packageFile, $helper)
    {
        $this->db = $db;

        $this->ff = $ff;

        $this->helper = $helper;

        $allowedImageMimeTypes = [];
        $allowedImageSizes = [];
        $allowedFileMimeTypes = [];

        foreach ($packageFile['settings']['allowedImageMimeTypes'] as $imageMimeTypes) {
            array_push($allowedImageMimeTypes, $imageMimeTypes['id']);
        }
        foreach ($packageFile['settings']['allowedImageSizes'] as $imageSizes) {
            array_push($allowedImageSizes, $imageSizes['id']);
        }
        foreach ($packageFile['settings']['allowedFileMimeTypes'] as $fileMimeTypes) {
            array_push($allowedFileMimeTypes, $fileMimeTypes['id']);
        }

        $this->addToDb(
            'Public',
            'local',
            'public',
            $allowedImageMimeTypes,
            $allowedImageSizes,
            $allowedFileMimeTypes
        );

        $this->addToDb(
            'Private',
            'local',
            'private',
            $allowedImageMimeTypes,
            $allowedImageSizes,
            $allowedFileMimeTypes
        );
    }

    protected function addToDb($name, $type, $permission, $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes)
    {
        $maxFilesize = toBytes(ini_get('upload_max_filesize'));
        $maxPostsize = toBytes(ini_get('post_max_size'));

        if ($maxPostsize >= $maxFilesize) {
            $maxBytes = $maxFilesize;
        } else {
            $maxBytes = $maxPostsize;
        }

        $storage =
            [
                'name'                          => $name,
                'type'                          => $type,
                'description'                   => '',
                'permission'                    => $permission,
                'allowed_image_mime_types'      => $this->helper->encode($allowedImageMimeTypes),
                'allowed_image_sizes'           => $this->helper->encode($allowedImageSizes),
                'images_path'                   => 'images',
                'cache_path'                    => 'cache',
                'max_image_size'                => 2000,
                'default_image_quality'         => 100,
                'max_image_file_size'           => $maxBytes,
                'allowed_file_mime_types'       => $this->helper->encode($allowedFileMimeTypes),
                'data_path'                     => 'data',
                'max_data_file_size'            => $maxBytes
            ];

        if ($this->db) {
            $this->db->insertAsDict('basepackages_storages', $storage);
        }

        if ($this->ff) {
            $storageStore = $this->ff->store('basepackages_storages');

            $storageStore->updateOrInsert($storage);
        }
    }
}