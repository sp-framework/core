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

    protected $minMultiWordsChars = 4;

    protected $folderPermissions = 0777;

    protected $reIndexIndexes = [];

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

    public function setIndex($content, $remove = false, $reIndex = false)
    {
        if (!$content) {
            return false;
        }

        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        $contentId = $content['id'];

        IoHelper::createFolder($this->indexesPath, $this->folderPermissions);

        foreach ($this->indexes as $index) {
            if (isset($content[$index])) {
                IoHelper::createFolder($this->indexesPath . $index . '/', $this->folderPermissions);

                if ($this->multiWords === true) {
                    if (is_string($content[$index])) {
                        $contentArr = explode($this->multiWordsSeparator, $content[$index]);

                        if (count($contentArr) > 1) {
                            foreach ($contentArr as $contentWord) {
                                $contentWord = strtolower($contentWord);

                                if (strlen($contentWord) < $this->minMultiWordsChars) {
                                    continue;
                                }

                                $indexChars = strtolower(mb_substr($contentWord, 0, $this->minIndexChars, 'UTF-8'));

                                if (str_contains($indexChars, '/')) {//this will result in subdirectories
                                    continue;
                                }

                                if (!checkCtype($contentWord, 'alnum')) {//Ignore Special chars
                                    continue;
                                }

                                if ($reIndex) {
                                    $this->addToReindexIndexes($contentId, $index, $indexChars, $contentWord);
                                } else {
                                    $this->writeIndex($contentId, $index, $indexChars, $contentWord, $remove);
                                }
                            }
                        } else {
                            if (strlen($content[$index]) === 10 &&
                                str_contains($content[$index], '-') &&
                                substr_count($content[$index], '-') === 2
                            ) {
                                try {
                                    $indexCharsIsDate = new \DateTime($content[$index]);
                                } catch (\throwable $e) {
                                    continue;
                                }

                                if ($indexCharsIsDate) {
                                    $content[$index] = $indexCharsIsDate->getTimestamp();

                                    if ($reIndex) {
                                        $this->addToReindexIndexes($contentId, $index, $content[$index], $content[$index]);
                                    } else {
                                        $this->writeIndex($contentId, $index, $content[$index], $content[$index], $remove);
                                    }

                                    continue;
                                }
                            }

                            $content[$index] = strtolower($content[$index]);

                            $indexChars = strtolower(mb_substr($content[$index], 0, $this->minIndexChars, 'UTF-8'));

                            if (str_contains($indexChars, '/')) {//this will result in subdirectories
                                continue;
                            }

                            if (!checkCtype($content[$index], 'alnum')) {//Ignore Special chars
                                continue;
                            }

                            if ($reIndex) {
                                $this->addToReindexIndexes($contentId, $index, $indexChars, $content[$index]);
                            } else {
                                $this->writeIndex($contentId, $index, $indexChars, $content[$index], $remove);
                            }
                        }
                    } else {
                        if (is_bool($content[$index])) {
                            if ($content[$index] === true) {
                                $content[$index] = 'true';
                            } else {
                                $content[$index] = 'false';
                            }
                        }

                        if ($reIndex) {
                            $this->addToReindexIndexes($contentId, $index, $content[$index], $content[$index]);
                        } else {
                            $this->writeIndex($contentId, $index, $content[$index], $content[$index], $remove);
                        }
                    }
                } else {
                    if (is_string($content[$index])) {
                        if (strlen($content[$index]) === 10 &&
                            str_contains($content[$index], '-') &&
                            substr_count($content[$index], '-') === 2
                        ) {
                            try {
                                $indexCharsIsDate = new \DateTime($content[$index]);
                            } catch (\throwable $e) {
                                continue;
                            }

                            if ($indexCharsIsDate) {
                                $content[$index] = $indexCharsIsDate->getTimestamp();

                                if ($reIndex) {
                                    $this->addToReindexIndexes($contentId, $index, $content[$index], $content[$index]);
                                } else {
                                    $this->writeIndex($contentId, $index, $content[$index], $content[$index], $remove);
                                }

                                continue;
                            }
                        }

                        if (strlen($content[$index]) < $this->minIndexChars) {
                            continue;
                        }

                        $content[$index] = strtolower($content[$index]);

                        $indexChars = strtolower(mb_substr($content[$index], 0, $this->minIndexChars, 'UTF-8'));

                        if (str_contains($indexChars, '/')) {//this will result in subdirectories
                            continue;
                        }


                        if (!checkCtype($content[$index], 'alnum')) {//Ignore Special chars
                            continue;
                        }

                        if ($reIndex) {
                            $this->addToReindexIndexes($contentId, $index, $indexChars, $content[$index]);
                        } else {
                            $this->writeIndex($contentId, $index, $indexChars, $content[$index], $remove);
                        }
                    } else {
                        if (is_bool($content[$index])) {
                            if ($content[$index] === true) {
                                $content[$index] = 'true';
                            } else {
                                $content[$index] = 'false';
                            }
                        }

                        if ($reIndex) {
                            $this->addToReindexIndexes($contentId, $index, $content[$index], $content[$index]);
                        } else {
                            $this->writeIndex($contentId, $index, $content[$index], $content[$index], $remove);
                        }
                    }
                }
            }
        }
    }

    protected function addToReindexIndexes($contentId, $index, $indexChars, $content)
    {
        if (!isset($this->reIndexIndexes[$index])) {
            $this->reIndexIndexes[$index] = [];
        }

        if (!isset($this->reIndexIndexes[$index][$indexChars])) {
            $this->reIndexIndexes[$index][$indexChars] = [];
        }

        if (!isset($this->reIndexIndexes[$index][$indexChars][$content])) {
            $this->reIndexIndexes[$index][$indexChars][$content] = [];
        }

        if (!in_array($contentId, $this->reIndexIndexes[$index][$indexChars][$content])) {
            array_push($this->reIndexIndexes[$index][$indexChars][$content], $contentId);
        }
    }

    protected function writeIndex($contentId, $index, $indexChars, $content, $remove = false)
    {
        try {
            $indexFile = $this->getIndex($index, $indexChars);

            $indexJson = json_decode($indexFile, true);
        } catch (\Exception $e) {
            $indexJson = [];
        }

        if (isset($indexJson[$content])) {
            if ($remove) {
                $key = array_search($contentId, $indexJson[$content]);

                if ($key !== false) {
                    unset($indexJson[$content][$key]);
                }

                if (count($indexJson[$content]) === 0) {
                    return IoHelper::deleteFile($this->indexesPath . $index . '/' . $indexChars . '.json');
                }

                $indexJson[$content] = array_values($indexJson[$content]);
            } else {
                if (!in_array($contentId, $indexJson[$content])) {
                    array_push($indexJson[$content], $contentId);
                }
            }
        } else {
            if (!$remove) {
                $indexJson[$content] = [$contentId];
                $indexJson[$content] = array_values($indexJson[$content]);
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

        $scanDir = scandir($dataPath);
        $files = [];
        array_walk($scanDir, function($file) use (&$files) {
            if ($file !== '..' && $file !== '.') {
                array_push($files, (int) str_replace('.json', '', $file));
            }
        });
        sort($files);

        foreach ($files as $entry) {
            $documentPath = $dataPath . $entry . '.json';

            try {
                $data = IoHelper::getFileContent($documentPath);

                $this->setIndex($data, false, true);
            } catch (Exception $exception) {
                continue;
            }
        }

        //We write all index files here
        foreach ($this->reIndexIndexes as $index => $files) {
            foreach ($files as $content => $values) {
                IoHelper::writeContentToFile($this->indexesPath . $index . '/' . $content . '.json', json_encode($values));
            }
        }
    }
}