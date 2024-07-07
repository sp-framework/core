<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use Closure;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IndexHandler;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\JsonException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class IoHelper
{
    public static function checkWrite(string $path)
    {
        if (file_exists($path) === false) {
            $path = dirname($path);
        }

        if (!is_writable($path)) {
            throw new IOException(
                "Directory or file is not writable at \"$path\". Please change permission."
            );
        }
    }

    public static function checkRead(string $path)
    {
        if (!is_readable($path)) {
            throw new IOException(
                "Directory or file is not readable at \"$path\". Please change permission."
            );
        }
    }

    public static function getFileContent(string $filePath): string
    {
        self::checkRead($filePath);

        if (!file_exists($filePath)) {
            throw new IOException("File does not exist: $filePath");
        }

        return self::readFileContent($filePath);
    }

    public static function writeContentToFile(string $filePath, string $content, bool $index = false, Store $store = null)
    {
        self::checkWrite($filePath);

        if (file_put_contents($filePath, $content, LOCK_EX) === false) {
            throw new IOException("Could not write content to file. Please check permissions at: $filePath");
        }

        if ($index && $store) {
            $storeConfiguration = $store->getStoreConfiguration();

            if (count($storeConfiguration) > 0 &&
                $storeConfiguration['indexing']
            ) {
                try {
                    (new IndexHandler($storeConfiguration))->setIndex($content);
                } catch (\Exception $e) {
                    throw new IOException("Unable to set Index for : " . $filePath);
                }
            }
        }
    }

    public static function checkFolder(string $folderPath): bool
    {
        if (file_exists($folderPath) === true) {
            return true;
        }

        return false;
    }

    public static function deleteFolder(string $folderPath): bool
    {
        self::checkWrite($folderPath);

        $it = new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            self::checkWrite($file);

            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($folderPath);
    }

    public static function createFolder(string $folderPath, int $chmod)
    {
        if (file_exists($folderPath) === true) {
            return;
        }

        self::checkWrite($folderPath);

        if (!file_exists($folderPath) &&
            !mkdir($folderPath, $chmod, true) &&
            !is_dir($folderPath)
        ) {
            throw new IOException(
                'Unable to create the a directory at ' . $folderPath
            );
        }
    }

    public static function updateFileContent(string $filePath, Closure $updateContentFunction): string
    {
        self::checkRead($filePath);
        self::checkWrite($filePath);

        $content = $updateContentFunction(self::readFileContent($filePath));

        if (!is_string($content)) {
            $encodedContent = json_encode($content);

            if ($encodedContent === false) {
                $content = (!is_object($content) && !is_array($content) && !is_null($content)) ? $content : gettype($content);

                throw new JsonException("Could not encode content with json_encode. Content: \"$content\".");
            }

            $content = $encodedContent;
        }

        if (file_put_contents($filePath, $content, LOCK_EX) === false) {
            throw new IOException("Could not write content to file. Please check permissions at: $filePath");
        }

        return $content;
    }

    public static function deleteFile(string $filePath): bool
    {
        if (false === file_exists($filePath)) {
            return true;
        }

        try {
            self::checkWrite($filePath);
        } catch(Exception $exception) {
            return false;
        }

        return (@unlink($filePath) && !file_exists($filePath));
    }

    public static function deleteFiles(array $filePaths): bool
    {
        foreach ($filePaths as $filePath){
            if (true === file_exists($filePath)) {
                try {
                    self::checkWrite($filePath);

                    if (false === @unlink($filePath) || file_exists($filePath)) {
                        return false;
                    }
                } catch (Exception $exception) {
                    // TODO trigger a warning or exception
                    return false;
                }
            }
        }

        return true;
    }

    public static function secureStringForFileAccess(string $string): string
    {
        return (str_replace(array(".", "/", "\\"), "", $string));
    }

    /**
    * Appends a slash ("/") to the given directory path if there is none.
    */
    public static function normalizeDirectory(string &$directory)
    {
        if (!empty($directory) && substr($directory, -1) !== "/") {
            $directory .= "/";
        }
    }

    public static function countFolderContent(string $folder, $recount = false): array
    {
        self::checkRead($folder);

        $fi = new \FilesystemIterator($folder, \FilesystemIterator::SKIP_DOTS);

        $count = [];
        $count['totalEntries'] = iterator_count($fi);

        if ($recount && $count['totalEntries'] > 0) {
            $files = iterator_to_array($fi);

            foreach ($files as $key => &$file) {
                $file = (int) str_replace('.json', '', $file->getFileName());
            }

            $count['lastId'] = max($files);
        }

        return $count;
    }

    public static function getFolderList(string $folderToList): array
    {
        self::checkRead($folderToList);

        $folders = glob($folderToList . '*' , GLOB_ONLYDIR);

        return $folders;
    }

    protected static function readFileContent($filePath)
    {
        $content = false;
        $fp = fopen($filePath, 'rb');

        if (flock($fp, LOCK_SH)) {
            $content = stream_get_contents($fp);
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        if ($content === false)  {
            throw new IOException("Could not retrieve the content of a file. Please check permissions at: $filePath");
        }

        return $content;
    }
}