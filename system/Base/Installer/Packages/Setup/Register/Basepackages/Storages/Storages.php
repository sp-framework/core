<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\Storages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Storages;

/**
 * Seeds default local Public and Private file storage records.
 */
class Storages
{
    /**
     * PDO database connection adapter.
     *
     * @var mixed
     */
    protected mixed $db = null;

    /**
     * FlatFile database manager.
     *
     * @var mixed
     */
    protected mixed $ff = null;

    /**
     * Helpers service instance.
     *
     * @var mixed
     */
    protected mixed $helper = null;

    /**
     * Registers Public and Private storage configurations.
     *
     * @param mixed                $db          PDO database connection adapter.
     * @param mixed                $ff          FlatFile database manager.
     * @param array<string, mixed> $packageFile Package metadata array.
     * @param mixed                $helper      Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $packageFile, mixed $helper): void
    {
        $this->db = $db;
        $this->ff = $ff;
        $this->helper = $helper;

        $allowedImageMimeTypes = [];
        $allowedImageSizes = [];
        $allowedFileMimeTypes = [];

        if (isset($packageFile['settings']['allowedImageMimeTypes']) && is_array($packageFile['settings']['allowedImageMimeTypes'])) {
            foreach ($packageFile['settings']['allowedImageMimeTypes'] as $imageMimeTypes) {
                if (isset($imageMimeTypes['id'])) {
                    $allowedImageMimeTypes[] = $imageMimeTypes['id'];
                }
            }
        }

        if (isset($packageFile['settings']['allowedImageSizes']) && is_array($packageFile['settings']['allowedImageSizes'])) {
            foreach ($packageFile['settings']['allowedImageSizes'] as $imageSizes) {
                if (isset($imageSizes['id'])) {
                    $allowedImageSizes[] = $imageSizes['id'];
                }
            }
        }

        if (isset($packageFile['settings']['allowedFileMimeTypes']) && is_array($packageFile['settings']['allowedFileMimeTypes'])) {
            foreach ($packageFile['settings']['allowedFileMimeTypes'] as $fileMimeTypes) {
                if (isset($fileMimeTypes['id'])) {
                    $allowedFileMimeTypes[] = $fileMimeTypes['id'];
                }
            }
        }

        $this->addToDb('Public', 'local', 'public', $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes);
        $this->addToDb('Private', 'local', 'private', $allowedImageMimeTypes, $allowedImageSizes, $allowedFileMimeTypes);
    }

    /**
     * Persists storage entry into storages table and storage provider store.
     *
     * @param string              $name                  Storage name.
     * @param string              $type                  Storage adapter type (local).
     * @param string              $permission            Visibility permission (public, private).
     * @param array<int, mixed>   $allowedImageMimeTypes Allowed image MIME IDs.
     * @param array<int, mixed>   $allowedImageSizes     Allowed image sizes.
     * @param array<int, mixed>   $allowedFileMimeTypes  Allowed document MIME IDs.
     *
     * @return void
     */
    protected function addToDb(
        string $name,
        string $type,
        string $permission,
        array $allowedImageMimeTypes,
        array $allowedImageSizes,
        array $allowedFileMimeTypes
    ): void {
        $maxFilesize = function_exists('toBytes') ? toBytes((string) ini_get('upload_max_filesize')) : 2097152;
        $maxPostsize = function_exists('toBytes') ? toBytes((string) ini_get('post_max_size')) : 8388608;

        $maxBytes = ($maxPostsize >= $maxFilesize) ? $maxFilesize : $maxPostsize;

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
            $storagesStore = $this->ff->store('basepackages_storages');

            $storagesStore->updateOrInsert($storage);
        }
    }
}