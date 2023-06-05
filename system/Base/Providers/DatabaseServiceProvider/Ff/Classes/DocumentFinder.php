<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use Exception;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Query;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class DocumentFinder
{
    protected $storePath;

    protected $queryBuilderProperties;

    protected $primaryKey;

    protected $store;

    protected $storeConfiguration;

    public function __construct(string $storePath, array $queryBuilderProperties, string $primaryKey, $store)
    {
        $this->storePath = $storePath;

        $this->queryBuilderProperties = $queryBuilderProperties;

        $this->primaryKey = $primaryKey;

        $this->store = $store;

        $this->storeConfiguration = $this->store->getStoreConfiguration();
    }

    public function findDocuments(bool $getOneDocument, bool $reduceAndJoinPossible): array
    {
        $queryBuilderProperties = $this->queryBuilderProperties;
        $dataPath = $this->getDataPath();
        $primaryKey = $this->primaryKey;

        $found = [];

        IoHelper::checkRead($dataPath);

        $conditions = $queryBuilderProperties["whereConditions"];
        $distinctFields = $queryBuilderProperties["distinctFields"];
        $listOfJoins = $queryBuilderProperties["listOfJoins"];
        $search = $queryBuilderProperties["search"];
        $searchOptions = $queryBuilderProperties["searchOptions"];
        $groupBy = $queryBuilderProperties["groupBy"];
        $havingConditions = $queryBuilderProperties["havingConditions"];
        $fieldsToSelect = $queryBuilderProperties["fieldsToSelect"];
        $orderBy = $queryBuilderProperties["orderBy"];
        $skip = $queryBuilderProperties["skip"];
        $limit = $queryBuilderProperties["limit"];
        $fieldsToExclude = $queryBuilderProperties["fieldsToExclude"];

        unset($queryBuilderProperties);

        if ($this->storeConfiguration['indexing']) {
            if (count($conditions) > 0) {
                foreach ($conditions as $condition) {
                    if (isset($condition[0]) &&
                        in_array($condition[0], $this->storeConfiguration['indexes'])
                    ) {
                        $keyword = trim($condition[2], '%');//This needs to extend

                        if (strlen($keyword) < $this->storeConfiguration['min_index_chars']) {
                            continue;
                        }

                        $indexChars = strtolower(substr($keyword, 0, $this->storeConfiguration['min_index_chars']));

                        try {
                            $indexFile =
                                IoHelper::getFileContent($this->storeConfiguration['indexesPath'] . $condition[0] . '/' . $indexChars . '.json');

                            $indexJson = json_decode($indexFile, true);

                            if (count($indexJson) > 0) {
                                foreach ($indexJson as $key => $ids) {
                                    $key = strtolower($key);

                                    if (strtolower($condition[1]) === 'like') {
                                        if (str_starts_with($key, strtolower($keyword))) {
                                            foreach ($ids as $id) {
                                                $found[] = $this->store->findById($id);
                                            }
                                        }
                                    } else if ($condition[1] === '=' ||
                                               $condition[1] === '==='
                                    ) {
                                        if ($key === strtolower($keyword)) {
                                            foreach ($ids as $id) {
                                                $found[] = $this->store->findById($id);
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $found = [];
                        }
                    }
                }
            }
        }

        if (count($found) === 0) {
            if ($handle = opendir($dataPath)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry === "." || $entry === "..") {
                        continue;
                    }

                    $documentPath = $dataPath . $entry;

                    try {
                        $data = IoHelper::getFileContent($documentPath);
                    } catch (Exception $exception) {
                        continue;
                    }

                    $data = @json_decode($data, true);

                    if (!is_array($data)) {
                        continue;
                    }

                    $storePassed = true;

                    if (!empty($conditions)) {
                        $storePassed = ConditionsHandler::handleWhereConditions($conditions, $data);
                    }

                    if ($storePassed === true && count($distinctFields) > 0) {
                        $storePassed = ConditionsHandler::handleDistinct($found, $data, $distinctFields);
                    }

                    if ($storePassed === true) {
                        $found[] = $data;

                        if ($getOneDocument === true) {
                            break;
                        }
                    }
                }

                closedir($handle);
            }
        }

        if ($reduceAndJoinPossible === true) {
            DocumentReducer::joinData($found, $listOfJoins);
        }

        if (count($found) > 0) {
            self::performSearch($found, $search, $searchOptions);
        }

        if ($reduceAndJoinPossible === true && !empty($groupBy) && count($found) > 0) {
            DocumentReducer::handleGroupBy(
                $found,
                $groupBy,
                $fieldsToSelect
            );
        }

        if ($reduceAndJoinPossible === true && empty($groupBy) && count($found) > 0) {
            DocumentReducer::selectFields($found, $primaryKey, $fieldsToSelect);
        }

        if (count($found) > 0) {
            self::handleHaving($found, $havingConditions);
        }

        if ($reduceAndJoinPossible === true && count($found) > 0) {
            DocumentReducer::excludeFields($found, $fieldsToExclude);
        }

        if (count($found) > 0) {
            self::sort($found, $orderBy);
        }

        if (count($found) > 0) {
            self::skip($found, $skip);
        }

        if (count($found) > 0) {
            self::limit($found, $limit);
        }

        return $found;
    }

    protected function getDataPath(): string
    {
        return $this->storePath . Store::dataDirectory;
    }

    protected static function sort(array &$found, array $orderBy)
    {
        if (!empty($orderBy)) {

            $resultSortArray = [];

            foreach ($orderBy as $orderByClause) {
                $order = $orderByClause['order'];
                $fieldName = $orderByClause['fieldName'];

                $arrayColumn = [];

                foreach ($found as $value) {
                    $arrayColumn[] = NestedHelper::getNestedValue($fieldName, $value);
                }

                $resultSortArray[] = $arrayColumn;

                $resultSortArray[] = ($order === 'asc') ? SORT_ASC : SORT_DESC;

            }

            if (!empty($resultSortArray)) {
                $resultSortArray[] = &$found;
                array_multisort(...$resultSortArray);
            }

            unset($resultSortArray);
        }
    }

    protected static function skip(array &$found, $skip)
    {
        if (empty($skip) || $skip <= 0) {
            return;
        }

        $found = array_slice($found, $skip);
    }

    protected static function limit(array &$found, $limit)
    {
        if (empty($limit) || $limit <= 0) {
            return;
        }

        $found = array_slice($found, 0, $limit);
    }

    protected static function performSearch(array &$found, array $search, array $searchOptions)
    {
        if (empty($search)) {
            return;
        }

        $minLength = $searchOptions["minLength"];
        $searchScoreKey = $searchOptions["scoreKey"];
        $searchMode = $searchOptions["mode"];
        $searchAlgorithm = $searchOptions["algorithm"];

        $scoreMultiplier = 64;
        $encoding = "UTF-8";

        $fields = $search["fields"];
        $query = $search["query"];
        $lowerQuery = mb_strtolower($query, $encoding);
        $exactQuery  = preg_quote($query, "/");

        $fieldsLength = count($fields);

        $highestScore = $scoreMultiplier ** $fieldsLength;

        $searchWords = preg_replace('/(\s)/u', ',', $query);
        $searchWords = explode(",", $searchWords);

        $prioritizeAlgorithm = (in_array($searchAlgorithm, [
            Query::SEARCH_ALGORITHM["prioritize"],
            Query::SEARCH_ALGORITHM["prioritize_position"]
        ], true));

        $positionAlgorithm = ($searchAlgorithm === Query::SEARCH_ALGORITHM["prioritize_position"]);

        $temp = [];
        foreach ($searchWords as $searchWord) {
            if (strlen($searchWord) >= $minLength) {
                $temp[] = $searchWord;
            }
        }

        $searchWords = $temp;
        unset($temp);

        $searchWords = array_map(static function($value){
            return preg_quote($value, "/");
        }, $searchWords);

        if ($searchMode === "and") {
            $preg = "";

            foreach ($searchWords as $searchWord) {
                $preg .= "(?=.*".$searchWord.")";
            }

            $preg = '/^' . $preg . '.*/im';

            $pregOr = '!(' . implode('|', $searchWords) . ')!i';
        } else {
            $preg = '!(' . implode('|', $searchWords) . ')!i';
        }

        foreach ($found as $foundKey => &$document) {
            $searchHits = 0;

            $searchScore = 0;

            foreach ($fields as $key => $field) {
                if ($prioritizeAlgorithm) {
                    $score = $highestScore / ($scoreMultiplier ** $key);
                } else {
                    $score = $scoreMultiplier;
                }

                $value = NestedHelper::getNestedValue($field, $document);

                if (!is_string($value) || $value === "") {
                    continue;
                }

                $lowerValue = mb_strtolower($value, $encoding);

                if ($lowerQuery === $lowerValue) {
                    $searchHits++;
                    $searchScore += 16 * $score;
                } elseif ($positionAlgorithm && mb_strpos($lowerValue, $lowerQuery, 0, $encoding) === 0) {
                    $searchHits++;
                    $searchScore += 8 * $score;
                } elseif ($matches = preg_match_all('!' . $exactQuery . '!i', $value)) {
                    $searchHits += $matches;
                    $searchScore += $matches * 2 * $score;

                    if ($searchAlgorithm === Query::SEARCH_ALGORITHM["hits_prioritize"]) {
                        $searchScore += $matches * ($fieldsLength - $key);
                    }
                }

                $matchesArray = [];

                $matches = ($searchMode === "and") ? preg_match($preg, $value) : preg_match_all($preg, $value, $matchesArray, PREG_OFFSET_CAPTURE);

                if ($matches) {
                    $searchHits += $matches;
                    $searchScore += $matches * $score;

                    if ($searchAlgorithm === Query::SEARCH_ALGORITHM["hits_prioritize"]) {
                        $searchScore += $matches * ($fieldsLength - $key);
                    }

                    if ($searchMode === "and" &&
                        isset($pregOr) &&
                        ($matches = preg_match_all($pregOr, $value, $matchesArray, PREG_OFFSET_CAPTURE))
                    ) {
                        $searchHits += $matches;
                        $searchScore += $matches * $score;
                    }
                }

                if ($positionAlgorithm && $matches && !empty($matchesArray)) {
                    $hitPosition = $matchesArray[0][0][1];

                    if (!is_int($hitPosition) || !($hitPosition > 0)) {
                        $hitPosition = 1;
                    }

                    $searchScore += ($score / $highestScore) * ($hitPosition / ($hitPosition * $hitPosition));
                }
            }

            if ($searchHits > 0) {
                if (!is_null($searchScoreKey)) {
                    $document[$searchScoreKey] = $searchScore;
                }
            } else {
                unset($found[$foundKey]);
            }
        }
    }

    protected static function handleHaving(array &$found, array $havingConditions)
    {
        if (empty($havingConditions)) {
            return;
        }

        foreach ($found as $key => $document) {
            if (false === ConditionsHandler::handleWhereConditions($havingConditions, $document)) {
                unset($found[$key]);
            }
        }
    }
}