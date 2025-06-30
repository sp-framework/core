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

    protected $minIndexChars = 3;

    protected $multiWords = true;

    protected $multiWordsSeparator = '+';

    protected $minMultiWordsChars = 4;

    public function __construct(string $storePath, array $queryBuilderProperties, string $primaryKey, $store)
    {
        $this->storePath = $storePath;

        $this->queryBuilderProperties = $queryBuilderProperties;

        $this->primaryKey = $primaryKey;

        $this->store = $store;

        $this->storeConfiguration = $this->store->getStoreConfiguration();

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

        $indexSearched = false;

        //Note: for now index searches are using "OR" condition. If there are 2 more conditions to search,
        //both conditions data will be searched. This needs to be extended to include keywords like "OR" and "AND"
        if ($this->storeConfiguration['readIndex']) {
            if (count($conditions) > 0) {
                foreach ($conditions as $conditionKey => $condition) {
                    $conditionArr = $condition;

                    if (is_array($condition[0])) {
                        $found = $this->processIndexes($condition[0], $found, $skip, $limit);

                        if (count($found) > 0) {
                            //Once our first condition is met, we do not process index anymore. We just process the data of first condition
                            //This is like filtering. ex: we first search for data meeting one condition and once we have the data, we filter it
                            //using following conditions.
                            unset($condition[0]);

                            foreach ($condition as $conditionArr) {
                                //OR Condition
                                if (is_string($conditionArr[1]) && strtolower($conditionArr[1]) === 'or') {
                                    foreach ($found as $foundKey => $foundValue) {
                                        if (isset($foundValue[$conditionArr[0][0]]) &&
                                            isset($foundValue[$conditionArr[2][0]])
                                        ) {
                                            if ($foundValue[$conditionArr[0][0]] === $conditionArr[0][2] ||
                                                $foundValue[$conditionArr[2][0]] === $conditionArr[2][2]
                                            ) {
                                                continue;
                                            }

                                            unset($found[$foundKey]);
                                        }
                                    }
                                } else {//AndCondition
                                    foreach ($found as $foundKey => $foundValue) {
                                        if (isset($foundValue[$conditionArr[0]])) {
                                            if ($foundValue[$conditionArr[0]] !== $conditionArr[2]) {
                                                unset($found[$foundKey]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $this->processIndexes($conditionArr, $found, $skip, $limit);
                    }
                }
            }

            $found = msort($found, 'id');
        }

        if (!$indexSearched && count($found) === 0) {
            $scanDir = scandir($dataPath);
            $files = [];
            array_walk($scanDir, function($file) use (&$files) {
                if ($file !== '..' && $file !== '.') {
                    array_push($files, (int) str_replace('.json', '', $file));
                }
            });
            sort($files);
            if ($limit && count($conditions) === 0 && count($files) > 0) {
                self::skip($files, $skip);
                self::limit($files, $limit);
            }

            foreach ($files as $entry) {
                $documentPath = $dataPath . $entry . '.json';

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
        }

        if (count($found) > 0) {
            if ($reduceAndJoinPossible === true) {
                DocumentReducer::joinData($found, $listOfJoins);
            }

            self::performSearch($found, $search, $searchOptions);

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

            self::handleHaving($found, $havingConditions);

            if ($reduceAndJoinPossible === true && count($found) > 0) {
                DocumentReducer::excludeFields($found, $fieldsToExclude);
            }

            if (!$indexSearched && count($conditions) > 0) {
                self::sort($found, $orderBy);
                self::skip($found, $skip);
                self::limit($found, $limit);
            }
        }

        return $found;
    }

    protected function processIndexes($conditionArr, &$found, $skip, $limit)
    {
        if (isset($conditionArr[0]) &&
            in_array($conditionArr[0], $this->storeConfiguration['indexes'])
        ) {
            $indexSearched = true;

            //trim % (like), change space to + for multikeyword search.
            if (is_string($conditionArr[2])) {
                $keyword = str_replace(' ', '+', strtolower(trim($conditionArr[2], '%')));
            } else {
                $keyword = $conditionArr[2];
            }

            if ($this->multiWords === true && str_contains($keyword, '+')) {
                $keywordArr = explode('+', $keyword);

                foreach ($keywordArr as $key => $keyword) {
                    if (strlen($keyword) < $this->minMultiWordsChars) {
                        continue;
                    }

                    if (is_string($keyword)) {
                        $indexChars = strtolower(mb_substr($keyword, 0, $this->minIndexChars, 'UTF-8'));
                    } else {
                        $indexChars = $keyword;
                    }

                    $found = array_replace($found, $this->searchIndexes($conditionArr, $indexChars, $skip, $limit, $keyword));
                }
            } else {
                if (is_string($keyword)) {
                    if (strlen($keyword) < $this->minIndexChars) {
                        return [];
                    }

                    $indexChars = strtolower(mb_substr($keyword, 0, $this->minIndexChars, 'UTF-8'));
                } else {
                    $indexChars = $keyword;
                }

                $found = array_replace($found, $this->searchIndexes($conditionArr, $indexChars, $skip, $limit, $keyword));
            }
        }

        return $found ?? [];
    }

    protected function searchIndexes($condition, $indexChars, $skip, $limit, $keyword)
    {
        try {
            $indexFile = IoHelper::getFileContent(
                $this->storeConfiguration['indexesPath'] . $condition[0] . '/' . $indexChars . '.json'
            );

            $indexFile = strtolower($indexFile);

            $indexJson = json_decode($indexFile, true);

            if (count($indexJson) > 0) {
                if ($limit && count($indexJson) > 0) {
                    self::skip($indexJson, $skip);
                    self::limit($indexJson, $limit);
                }

                if (isset($indexJson[$keyword])) {
                    if (count($indexJson[$keyword]) === 1) {
                        $indexIdData = $this->store->findById($indexJson[$keyword][0]);

                        if ($indexIdData) {
                            $found[$indexIdData['id']] = $indexIdData;
                        }
                    } else {
                        foreach ($indexJson[$keyword] as $id) {
                            $indexIdData = $this->store->findById($id);

                            if ($indexIdData) {
                                $found[$indexIdData['id']] = $indexIdData;
                            }
                        }
                    }
                } else {
                    foreach ($indexJson as $key => $ids) {
                        if ($limit && count($ids) > $limit) {
                            self::skip($ids, $skip);
                            self::limit($ids, $limit);
                        }

                        $key = strtolower($key);

                        if (strtolower($condition[1]) === 'like') {
                            if (str_starts_with($key, $indexChars)) {
                                foreach ($ids as $id) {
                                    $indexIdData = $this->store->findById($id);

                                    if ($indexIdData) {
                                        $found[$indexIdData['id']] = $indexIdData;
                                    }
                                }
                            }
                        } else if ($condition[1] === '=' ||
                                   $condition[1] === '==='
                        ) {
                            if ($key === $indexChars) {
                                foreach ($ids as $id) {
                                    $indexIdData = $this->store->findById($id);

                                    if ($indexIdData) {
                                        $found[$indexIdData['id']] = $indexIdData;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $found = [];
        }

        return $found ?? [];
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

        $found = array_slice($found, $skip, null, true);
    }

    protected static function limit(array &$found, $limit)
    {
        if (empty($limit) || $limit <= 0) {
            return;
        }

        $found = array_slice($found, 0, $limit, true);
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