<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;

class IndexHandler
{
    protected $storeConfiguration;

    protected $indexesPath;

    protected $indexes = [];

    protected $minIndexChars = 3;

    protected $folderPermissions = 0777;

    public function __construct(array $storeConfiguration)
    {
        $this->storeConfiguration = $storeConfiguration;

        if (!isset($this->storeConfiguration['indexesPath'])) {
            throw new \Exception('Indexes Path missing from store configuration');
        }

        $this->indexesPath = $this->storeConfiguration['indexesPath'];

        if (!isset($this->storeConfiguration['indexes']) ||
            (isset($this->storeConfiguration['indexes']) &&
             is_array($this->storeConfiguration['indexes']) &&
             count($this->storeConfiguration['indexes']) === 0)
        ) {
            return false;
        }

        $this->indexes = $this->storeConfiguration['indexes'];

        if (isset($this->storeConfiguration['min_index_chars'])) {
            $this->minIndexChars = $this->storeConfiguration['min_index_chars'];
        }
    }

    public function setIndex(string $content)
    {
        $content = json_decode($content, true);

        $indexPointer = $content['id'];

        IoHelper::createFolder($this->indexesPath, $this->folderPermissions);

        foreach ($this->indexes as $index) {
            if (isset($content[$index])) {
                IoHelper::createFolder($this->indexesPath . $index . '/', $this->folderPermissions);
                if (is_string($content[$index])) {
                    $indexChars = strtolower(substr($content[$index], 0, $this->minIndexChars));
                } else {
                    $indexChars = $content[$index];
                }

                try {
                    $indexFile = $this->getIndex($index, $indexChars);

                    $indexJson = json_decode($indexFile, true);
                } catch (\Exception $e) {
                    $indexJson = [];
                }

                if (isset($indexJson[$content[$index]])) {
                    if (!in_array($indexPointer, $indexJson[$content[$index]])) {
                        array_push($indexJson[$content[$index]], $indexPointer);
                    }
                } else {
                    $indexJson[$content[$index]] = [$indexPointer];
                }

                IoHelper::writeContentToFile($this->indexesPath . $index . '/' . $indexChars . '.json', json_encode($indexJson));
            }
        }
    }

    public function getIndex($index, $indexChars)
    {
        return IoHelper::getFileContent($this->indexesPath . $index . '/' . $indexChars . '.json');
    }

    public function removeIndex($index, $indexChars)
    {
        return IoHelper::deleteFile($this->indexesPath . $index . '/' . $indexChars . '.json');
    }

    public function reIndex($dataPath = null)
    {
        IoHelper::deleteFolder($this->indexesPath);

        if (!$dataPath) {
            $dataPath = $this->storeConfiguration['storePath'] . 'data/';
        }

        if ($handle = opendir($dataPath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === "." || $entry === "..") {
                    continue;
                }

                $documentPath = $dataPath . $entry;

                try {
                    $data = IoHelper::getFileContent($documentPath);

                    $this->setIndex($data);
                } catch (\Exception $exception) {
                    continue;
                }
            }

            closedir($handle);
        }
    }
}