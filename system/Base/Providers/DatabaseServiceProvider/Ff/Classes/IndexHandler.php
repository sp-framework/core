<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;

class IndexHandler
{
    protected $storeConfiguration;

    protected $indexesPath;

    protected $indexes = [];

    protected $minIndexChars = 3;

    protected $multiWords = true;

    protected $multiWordsSeparator = ' ';

    protected $minMultiWordsChars = 5;

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
        if (isset($this->storeConfiguration['multi_words'])) {
            $this->multiWords = $this->storeConfiguration['multi_words'];
        }
        if (isset($this->storeConfiguration['multi_words_separator'])) {
            $this->multiWordsSeparator = $this->storeConfiguration['multi_words_separator'];
        }
        if (isset($this->storeConfiguration['min_multi_words_chars'])) {
            $this->minMultiWordsChars = $this->storeConfiguration['min_multi_words_chars'];
        }
    }

    public function setIndex($content, $remove = false)
    {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        $indexPointer = $content['id'];

        IoHelper::createFolder($this->indexesPath, $this->folderPermissions);

        foreach ($this->indexes as $index) {
            if (isset($content[$index])) {
                IoHelper::createFolder($this->indexesPath . $index . '/', $this->folderPermissions);

                if ($this->multiWords === true) {
                    if (is_string($content[$index])) {
                        $contentArr = explode($this->multiWordsSeparator, $content[$index]);

                        if (count($contentArr) > 1) {
                            foreach ($contentArr as $content) {
                                if (strlen($content) < $this->minMultiWordsChars) {
                                    continue;
                                }

                                $indexChars = strtolower(mb_substr($content, 0, $this->minIndexChars, 'UTF-8'));

                                if (str_contains($indexChars, '/')) {//this will result in subdirectories
                                    continue;
                                }

                                $this->writeIndex($indexPointer, $index, $indexChars, $content, $remove);
                            }
                        } else {
                            $indexChars = strtolower(mb_substr($content[$index], 0, $this->minIndexChars, 'UTF-8'));

                            if (str_contains($indexChars, '/')) {//this will result in subdirectories
                                continue;
                            }

                            $this->writeIndex($indexPointer, $index, $indexChars, $content[$index], $remove);
                        }
                    } else {
                        $this->writeIndex($indexPointer, $index, $content[$index], $content[$index], $remove);
                    }
                } else {
                    if (is_string($content[$index])) {
                        $indexChars = strtolower(mb_substr($content[$index], 0, $this->minIndexChars, 'UTF-8'));

                        if (str_contains($indexChars, '/')) {//this will result in subdirectories
                            continue;
                        }

                        $this->writeIndex($indexPointer, $index, $indexChars, $content[$index], $remove);
                    } else {
                        $this->writeIndex($indexPointer, $index, $content[$index], $content[$index], $remove);
                    }
                }
            }
        }
    }

    protected function writeIndex($indexPointer, $index, $indexChars, $content, $remove = false)
    {
        try {
            $indexFile = $this->getIndex($index, $indexChars);

            $indexJson = json_decode($indexFile, true);
        } catch (\Exception $e) {
            $indexJson = [];
        }

        if (isset($indexJson[$content])) {
            if ($remove) {
                $key = array_search($indexPointer, $indexJson[$content]);

                if ($key !== false) {
                    unset($indexJson[$content][$key]);
                }

                if (count($indexJson[$content]) === 0) {
                    return IoHelper::deleteFile($this->indexesPath . $index . '/' . $indexChars . '.json');
                }
            } else {
                if (!in_array($indexPointer, $indexJson[$content])) {
                    array_push($indexJson[$content], $indexPointer);
                }
            }
        } else {
            if (!$remove) {
                $indexJson[$content] = [$indexPointer];
            }
        }

        IoHelper::writeContentToFile($this->indexesPath . $index . '/' . $indexChars . '.json', json_encode($indexJson));
    }

    public function getIndex($index, $indexChars)
    {
        return IoHelper::getFileContent($this->indexesPath . $index . '/' . $indexChars . '.json');
    }

    public function removeFromIndex($content)
    {
        $this->setIndex($content, true);
    }

    public function resetIndex($remove, $add)
    {
        $this->setIndex($remove, true);
        $this->setIndex($add);
    }

    public function removeIndex($index, $indexChars)
    {
        return IoHelper::deleteFile($this->indexesPath . $index . '/' . $indexChars . '.json');
    }

    public function reIndex($dataPath = null)
    {
        if (IoHelper::checkFolder($this->indexesPath)) {
            IoHelper::deleteFolder($this->indexesPath);
        }

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