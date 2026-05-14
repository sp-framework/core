<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use Exception;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\ConditionsHandler;
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

    public function __construct(string $storePath, array $queryBuilderProperties, string $primaryKey, &$store)
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
                foreach ($conditions as $conditionKey => $conditionArr) {
                    if (is_array($conditionArr[0])) {
                        if (count($conditionArr) > 1) {
                            $conditionsCount = [];

                            $this->processIndexes($conditionArr[0], $found);

                            //Once our first condition is met, we do not process index anymore. We just process the data of first condition
                            //This is like filtering. ex: we first search for data meeting one condition and once we have the data, we filter it
                            //using following conditions.
                            // Else if it is AND search, we add more data to found and then strip it down.
                            foreach ($conditionArr as $conditionArrKey => $conditionArrCondition) {
                                if (is_array($conditionArrCondition[0])) {//For Or Condition
                                    continue;
                                }

                                if ($conditionArrCondition === 'AND' || $conditionArrCondition === 'OR') {
                                    continue;
                                }

                                if (isset($conditionsCount[$conditionArrCondition[0]])) {
                                    $this->processIndexes($conditionArrCondition, $found);

                                    array_push($conditionsCount[$conditionArrCondition[0]], $conditionArrKey);
                                } else {
                                    $conditionsCount[$conditionArrCondition[0]] = [$conditionArrKey];
                                }
                            }

                            if (count($found) > 0) {
                                $conditionArrKey = null;

                                foreach ($conditionArr as $conditionArrKey => $conditionArrConditions) {
                                    //OR Condition
                                    if (isset($conditionArrConditions[1]) && is_string($conditionArrConditions[1]) && strtolower($conditionArrConditions[1]) === 'or') {
                                        foreach ($found as $foundKey => $foundValue) {
                                            if (isset($foundValue[$conditionArrConditions[0][0]]) &&
                                                isset($foundValue[$conditionArrConditions[2][0]])
                                            ) {
                                                if (ConditionsHandler::verifyCondition($conditionArrConditions[0][1], $foundValue[$conditionArrConditions[0][0]], $conditionArrConditions[0][2])
                                                ) {
                                                    continue;
                                                }
                                                if (ConditionsHandler::verifyCondition($conditionArrConditions[2][1], $foundValue[$conditionArrConditions[2][0]], $conditionArrConditions[2][2])
                                                ) {
                                                    continue;
                                                }

                                                unset($found[$foundKey]);
                                            }
                                        }
                                    } else {//AndCondition
                                        foreach ($found as $foundKey => $foundValue) {
                                            if (isset($foundValue[$conditionArrConditions[0][0]])) {
                                                if (isset($conditionsCount[$conditionArrConditions[0][0]]) &&
                                                    count($conditionsCount[$conditionArrConditions[0][0]]) > 1
                                                ) {//OR conditions (multiple AND conditions)
                                                    $match = false;

                                                    foreach ($conditionsCount[$conditionArrConditions[0][0]] as $conditionsCountIndex => $conditionsCountKey) {
                                                        if (ConditionsHandler::verifyCondition($conditionArr[$conditionsCountKey][1], $foundValue[$conditionArrConditions[0][0]], $conditionArr[$conditionsCountKey][2])
                                                        ) {
                                                            if (strtolower($conditionArr[1]) === 'or') {
                                                                $match = true;

                                                                break;
                                                            } else if (strtolower($conditionArr[1]) === 'and') {
                                                                if ($conditionsCountIndex === count($conditionsCount[$conditionArrConditions[0][0]]) - 1) {
                                                                    $match = true;

                                                                    break;
                                                                }

                                                                continue;
                                                            }
                                                        }

                                                        if (strtolower($conditionArr[1]) === 'and') {//If the first condition is not met.
                                                            break;
                                                        }
                                                    }

                                                    if (!$match) {
                                                        unset($found[$foundKey]);
                                                    }
                                                } else {
                                                    if (ConditionsHandler::verifyCondition($conditionArrConditions[0][1], $foundValue[$conditionArrConditions[0][0]], $conditionArrConditions[0][2])) {
                                                        continue;
                                                    }

                                                    unset($found[$foundKey]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->processIndexes($conditionArr[0], $found, $skip, $limit);
                        }
                    } else {
                        $this->processIndexes($conditionArr, $found, $skip, $limit);
                    }
                }
            }

            if (count($found) > 0) {
                if (!$this->store->criteriaCount) {
                    $this->store->criteriaCount = count($found);
                }

                $found = msort($found, 'id');

                if (count($conditions) > 0) {
                    $indexSearched = true;
                }
            } else {
                if (count($conditions) > 0) {
                    $this->store->criteriaCount = null;
                }
            }
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

            if ($indexSearched && count($orderBy) > 0) {
                self::sort($found, $orderBy);
            }
        }

        return $found;
    }

    protected function processIndexes($conditionArr, &$found, $skip = 0, $limit = 0)
    {
        //Search for ID
        if (!in_array('id', $this->storeConfiguration['indexes'])) {
            array_push($this->storeConfiguration['indexes'], 'id');
        }

        if (isset($conditionArr[0]) &&
            in_array($conditionArr[0], $this->storeConfiguration['indexes'])
        ) {
            //trim % (like), change space to + for multikeyword search.
            if (is_string($conditionArr[2])) {
                $keyword = str_replace(' ', '+', strtolower(trim($conditionArr[2], '%')));
            } else {
                $keyword = $conditionArr[2];
            }

            if ($this->multiWords === true && !is_array($keyword) && str_contains($keyword, '+')) {
                $keywordArr = explode('+', $keyword);

                foreach ($keywordArr as $key => $keyword) {
                    if (strlen($keyword) < $this->minMultiWordsChars) {
                        continue;
                    }

                    if (is_string($keyword)) {
                        $indexChars = strtolower(mb_substr($keyword, 0, $this->minIndexChars, 'UTF-8'));
                    } else {
                        if (is_bool($keyword)) {
                            if ($keyword === true) {
                                $keyword = 'true';
                            } else {
                                $keyword = 'false';
                            }
                        }

                        $indexChars = $keyword;
                    }

                    $found = array_replace($found, $this->searchIndexes($conditionArr, $indexChars, $keyword, $skip, $limit));
                }

                if (count($found) > 0) {//match all keyword to narrow down search.
                    foreach ($found as $foundKey => $foundArr) {
                        $fieldString = strtolower($foundArr[$conditionArr[0]]);

                        $foundAllKeywords = true;

                        foreach ($keywordArr as $key => $keyword) {
                            if (!str_contains($fieldString, $keyword)) {
                                $foundAllKeywords = false;

                                break;
                            }
                        }

                        if (!$foundAllKeywords) {
                            unset($found[$foundKey]);
                        }
                    }
                }
            } else {
                if (is_string($keyword)) {
                    if (strlen($keyword) === 10 &&
                        str_contains($keyword, '-') &&
                        substr_count($keyword, '-') === 2
                    ) {
                        $keywordIsDate = new \DateTime($keyword);

                        if ($keywordIsDate) {
                            $indexChars = $conditionArr[2] = $keyword = $keywordIsDate->getTimestamp();
                            // trace([$conditionArr]);
                            $found = array_replace($found, $this->searchIndexes($conditionArr, $indexChars, $keyword, $skip, $limit));

                            return $found ?? [];
                        }
                    }

                    if (strlen($keyword) < $this->minIndexChars) {
                        return [];
                    }

                    $indexChars = strtolower(mb_substr($keyword, 0, $this->minIndexChars, 'UTF-8'));
                } else {
                    if (is_bool($keyword)) {
                        if ($keyword === true) {
                            $keyword = 'true';
                        } else {
                            $keyword = 'false';
                        }
                    }

                    $indexChars = $keyword;
                }

                $found = array_replace($found, $this->searchIndexes($conditionArr, $indexChars, $keyword, $skip, $limit));
            }
        }

        return $found ?? [];
    }

    protected function searchIndexes($condition, $indexChars, $keyword, $skip = 0, $limit = 0)
    {
        if ($condition[1] === '<' || $condition[1] === '<=' || $condition[1] === '>' || $condition[1] === '>=' || strtolower($condition[1]) === 'between') {
            if ($condition[0] === 'id') {
                $scanDir = scandir($this->storeConfiguration['storePath'] . 'data/');
            } else {
                $scanDir = scandir($this->storeConfiguration['indexesPath'] . $condition[0]);
            }

            $files = [];

            array_walk($scanDir, function($file) use (&$files, $condition) {
                if ($file !== '..' && $file !== '.') {
                    $fileName = (int) str_replace('.json', '', $file);

                    if ($condition[1] === '<') {
                        if ($fileName < (int) $condition[2]) {
                            array_push($files, $fileName);
                        }
                    } else if ($condition[1] === '<=') {
                        if ($fileName <= (int) $condition[2]) {
                            array_push($files, $fileName);
                        }
                    } else if ($condition[1] === '>') {
                        if ($fileName > (int) $condition[2]) {
                            array_push($files, $fileName);
                        }
                    } else if ($condition[1] === '>=') {
                        if ($fileName >= (int) $condition[2]) {
                            array_push($files, $fileName);
                        }
                    } else if (strtolower($condition[1]) === 'between') {
                        if ($fileName >= (int) $condition[2][0] &&
                            $fileName <= (int) $condition[2][1]
                        ) {
                            array_push($files, $fileName);
                        }
                    }
                }
            });

            sort($files);

            if ($condition[0] === 'id') {
                $this->processIndexJson($condition, $files, $found, null, $skip, $limit);
            } else {
                $indexJson = [];

                foreach ($files as $file) {
                    $indexFile = IoHelper::getFileContent(
                        $this->storeConfiguration['indexesPath'] . $condition[0] . '/' . $file . '.json'
                    );

                    $indexFile = strtolower($indexFile);

                    $fileJson = json_decode($indexFile, true);

                    if (count($fileJson) > 0) {
                        foreach ($fileJson as $dataIds) {
                            $indexJson = array_merge($indexJson, $dataIds);
                        }
                    }
                }

                if (count($indexJson) > 0) {
                    $this->store->criteriaCount = count($indexJson);

                    $this->processIndexJson($condition, $indexJson, $found, null, $skip, $limit);
                }
            }
        } else {
            try {
                $indexFile = IoHelper::getFileContent(
                    $this->storeConfiguration['indexesPath'] . $condition[0] . '/' . $indexChars . '.json'
                );

                $indexFile = strtolower($indexFile);

                $indexJson = json_decode($indexFile, true);

                if (count($indexJson) > 0) {
                    $this->processIndexJson($condition, $indexJson, $found, $keyword, $skip, $limit);
                }
            } catch (\Exception $e) {
                $found = [];
            }
        }

        return $found ?? [];
    }

    protected function processIndexJson($condition, $indexJson, &$found, $keyword = null, $skip = 0, $limit = 0)
    {
        if ($limit && count($indexJson) > 0) {
            self::skip($indexJson, $skip);
            self::limit($indexJson, $limit);
        }

        if (isset($indexJson[$keyword])) {
            if (count($indexJson[$keyword]) === 1) {
                $this->store->criteriaCount = 1;

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
                if (is_array($ids) && $limit && count($ids) > $limit) {
                    self::skip($ids, $skip);
                    self::limit($ids, $limit);
                }

                if (strtolower($condition[1]) === 'like') {
                    $key = strtolower($key);

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
                    $key = strtolower($key);

                    if ($key === $indexChars) {
                        foreach ($ids as $id) {
                            $indexIdData = $this->store->findById($id);

                            if ($indexIdData) {
                                $found[$indexIdData['id']] = $indexIdData;
                            }
                        }
                    }
                } else if ($condition[1] === '<' ||
                           $condition[1] === '<=' ||
                           $condition[1] === '>' ||
                           $condition[1] === '>=' ||
                           strtolower($condition[1]) === 'between'
                ) {
                    $indexIdData = $this->store->findById($ids);

                    if ($indexIdData) {
                        $found[$indexIdData['id']] = $indexIdData;
                    }
                }
            }
        }
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