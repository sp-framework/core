<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Storages;

use Phalcon\Helper\Json;

class Storages
{
    protected $db;

    public function register($db, $packageFile)
    {
        $this->db = $db;

        $this->addToDb(
            'Public',
            'local',
            'public',
                $packageFile['settings']['allowedImageMimeTypes'],
                $packageFile['settings']['allowedImageSizes'],
                $packageFile['settings']['allowedFileMimeTypes']
        );

        $this->addToDb(
            'Private',
            'local',
            'private',
                $packageFile['settings']['allowedImageMimeTypes'],
                $packageFile['settings']['allowedImageSizes'],
                $packageFile['settings']['allowedFileMimeTypes']
        );
    }

    protected function addToDb($name, $type, $permission, $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes)
    {
        $this->db->insertAsDict('basepackages_storages',
            [
                'name'                          => $name,
                'type'                          => $type,
                'description'                   => '',
                'permission'                    => $permission,
                'allowed_image_mime_types'      => Json::encode($allowedImageMimeTypes),
                'allowed_image_sizes'           => Json::encode($allowedImageSizes),
                'images_path'                   => 'images',
                'cache_path'                    => 'cache',
                'max_image_size'                => 2000,
                'default_image_quality'         => 100,
                'max_image_file_size'           => 10485760,
                'allowed_file_mime_types'       => Json::encode($allowedFileMimeTypes),
                'data_path'                     => 'data',
                'max_data_file_size'            => 10485760
            ]
        );
    }
}