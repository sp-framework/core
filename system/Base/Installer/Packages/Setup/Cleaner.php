<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;

/**
 * Cleans cache, variable logs, old cookies, temporary files, FlatFile folders, and backups.
 */
class Cleaner
{
    /**
     * Dependency injection container.
     *
     * @var mixed
     */
    protected mixed $container;

    /**
     * Flysystem local file storage adapter.
     *
     * @var mixed
     */
    protected mixed $localContent;

    /**
     * Basepackages manager service.
     *
     * @var mixed
     */
    protected mixed $basepackages;

    /**
     * OpCache service instance.
     *
     * @var mixed
     */
    protected mixed $opCache;

    /**
     * Cookies service instance.
     *
     * @var mixed
     */
    protected mixed $cookies;

    /**
     * Request service instance.
     *
     * @var mixed
     */
    protected mixed $request;

    /**
     * Cleaner constructor.
     *
     * @param mixed $container    DI container.
     * @param mixed $localContent Local file content adapter.
     * @param mixed $basepackages Basepackages manager instance.
     * @param mixed $opCache      OpCache manager instance.
     * @param mixed $cookies      Cookies manager instance.
     * @param mixed $request      Request instance.
     */
    public function __construct(
        mixed $container,
        mixed $localContent,
        mixed $basepackages,
        mixed $opCache = null,
        mixed $cookies = null,
        mixed $request = null
    ) {
        $this->container = $container;
        $this->localContent = $localContent;
        $this->basepackages = $basepackages;
        $this->opCache = $opCache;
        $this->cookies = $cookies;
        $this->request = $request;
    }

    /**
     * Cleans var/ runtime cache, logs, and temporary files.
     *
     * @throws FilesystemException|UnableToDeleteFile If deletion fails.
     *
     * @return bool True on success.
     */
    public function cleanVar(): bool
    {
        if ($this->basepackages && isset($this->basepackages->utils)) {
            $files = $this->basepackages->utils->init($this->container)->scanDir('var/');

            if (isset($files['files']) && is_array($files['files'])) {
                foreach ($files['files'] as $file) {
                    $fileName = (string) $file;
                    if (!str_contains($fileName, 'progress') &&
                        !str_contains($fileName, 'opcache') &&
                        !str_contains($fileName, 'pusher-') &&
                        !str_contains($fileName, 'messenger-')
                    ) {
                        if ($this->localContent && method_exists($this->localContent, 'delete')) {
                            $this->localContent->delete($fileName);
                        }
                    }
                }
            }
        }

        if ($this->opCache && method_exists($this->opCache, 'removeCache')) {
            $this->opCache->removeCache(null, 'core');
        }

        return true;
    }

    /**
     * Purges existing FlatFile .ff/ storage directories and files.
     *
     * @throws FilesystemException|UnableToDeleteFile|UnableToDeleteDirectory If deletion fails.
     *
     * @return bool True on success.
     */
    public function cleanOldFfs(): bool
    {
        if ($this->basepackages && isset($this->basepackages->utils)) {
            $files = $this->basepackages->utils->init($this->container)->scanDir('.ff/');

            if (isset($files['files']) && is_array($files['files'])) {
                foreach ($files['files'] as $file) {
                    if (str_contains((string) $file, '.ff') && $this->localContent && method_exists($this->localContent, 'delete')) {
                        $this->localContent->delete((string) $file);
                    }
                }
            }

            if (isset($files['dirs']) && is_array($files['dirs'])) {
                foreach ($files['dirs'] as $dir) {
                    if (str_contains((string) $dir, '.ff') && $this->localContent && method_exists($this->localContent, 'deleteDirectory')) {
                        $this->localContent->deleteDirectory((string) $dir);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Purges legacy API key directories from system/.api/.
     *
     * @throws FilesystemException|UnableToDeleteFile|UnableToDeleteDirectory If deletion fails.
     *
     * @return bool True on success.
     */
    public function cleanOldAPIKeys(): bool
    {
        if ($this->basepackages && isset($this->basepackages->utils)) {
            $files = $this->basepackages->utils->init($this->container)->scanDir('system/.api/');

            if (isset($files['files']) && is_array($files['files'])) {
                foreach ($files['files'] as $file) {
                    if (str_contains((string) $file, '.api') && $this->localContent && method_exists($this->localContent, 'delete')) {
                        $this->localContent->delete((string) $file);
                    }
                }
            }

            if (isset($files['dirs']) && is_array($files['dirs'])) {
                foreach ($files['dirs'] as $dir) {
                    if (str_contains((string) $dir, '.api') && $this->localContent && method_exists($this->localContent, 'deleteDirectory')) {
                        $this->localContent->deleteDirectory((string) $dir);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Purges legacy database and FlatFile backup directories.
     *
     * @return bool True on success.
     */
    public function cleanOldBackups(): bool
    {
        $dirs = ['.backupsdb/', '.backupsff/'];

        foreach ($dirs as $dir) {
            if ($this->basepackages && isset($this->basepackages->utils)) {
                $files = $this->basepackages->utils->init($this->container)->scanDir($dir);
                $this->cleanOldBackupsFiles($files);
            }
        }

        return true;
    }

    /**
     * Deletes individual backup files.
     *
     * @param array<string, mixed> $files Scanned directory files structure.
     *
     * @throws FilesystemException|UnableToDeleteFile If deletion fails.
     *
     * @return bool True on success.
     */
    protected function cleanOldBackupsFiles(array $files): bool
    {
        if (isset($files['files']) && is_array($files['files'])) {
            foreach ($files['files'] as $file) {
                if ($this->localContent && method_exists($this->localContent, 'delete')) {
                    $this->localContent->delete((string) $file);
                }
            }
        }

        return true;
    }

    /**
     * Clears authentication session cookies on domain setup.
     *
     * @return bool True on success.
     */
    public function cleanOldCookies(): bool
    {
        $cookieKey = 'SP';
        $host = ($this->request && method_exists($this->request, 'getHttpHost')) ? $this->request->getHttpHost() : 'localhost';

        if ($this->cookies && method_exists($this->cookies, 'set')) {
            $this->cookies->set($cookieKey, '0', 1, '/', false, $host, true);

            if (method_exists($this->cookies, 'get') && $this->cookies->get($cookieKey)) {
                $this->cookies->get($cookieKey)->setOptions(['samesite' => 'strict']);
            }

            $this->cookies->set('id', '0', 1, '/', false, $host, true);
            $this->cookies->set('Installer', '0', 1, '/', false, $host, true);

            if (method_exists($this->cookies, 'send')) {
                $this->cookies->send();
            }
        }

        return true;
    }
}
