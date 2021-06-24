<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Storages;

use Phalcon\Helper\Json;

class Storages
{
    protected $db;

    public function register($db)
    {
        $this->db = $db;

        $allowedImageMimeTypes =
            [
                "image/jpeg",
                "image/png",
                "image/gif"
            ];

        $allowedImageSizes =
            [
                "30",
                "80",
                "200",
                "800",
                "1200",
                "2000"
            ];

        $allowedFileMimeTypes =
            [
                "application/pdf",
                "text/plain",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "application/vnd.ms-excel",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "application/vnd.ms-powerpoint",
                "application/vnd.openxmlformats-officedocument.presentationml.presentation",
                "application/zip",
                "text/csv"
            ];

        $this->addToDb('Public', 'local', 'public', $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes);

        $this->addToDb('Private', 'local', 'private', $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes);
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