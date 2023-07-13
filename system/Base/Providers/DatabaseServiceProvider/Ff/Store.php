<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff;

use Exception;
use Opis\JsonSchema\Helper;
use Opis\JsonSchema\Validator;
use Phalcon\Helper\Arr;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Utils;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IndexHandler;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\NestedHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IdNotAllowedException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidConfigurationException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidDataException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidStoreException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\JsonException;

class Store
{
    protected $storeName = '';
    protected $storePath = '';
    protected $databasePath = '';

    protected $useCache = true;
    protected $defaultCacheLifetime = null;

    protected $indexesPath = '';
    protected $indexing = false;
    protected $minIndexChars = 3;
    protected $indexes = [];

    protected $uniqueFields = [];

    protected $primaryKey = "id";

    protected $searchOptions = [
        "minLength" => 2,
        "scoreKey" => "searchScore",
        "mode" => "or",
        "algorithm" => Query::SEARCH_ALGORITHM["hits"]
    ];

    protected $folderPermissions = 0777;

    protected $storeConfiguration = [];

    protected $storeSchema = [];

    public $data;

    const dataDirectory = "data/";

    public function __construct(string $storeName, string $databasePath, array $configuration = [], array $schema = [])
    {
        if (empty($storeName)) {
            throw new InvalidArgumentException('store name can not be empty');
        }
        $this->storeName = trim($storeName);
        IoHelper::normalizeDirectory($this->storeName);

        if (empty($databasePath)) {
            throw new InvalidArgumentException('data directory can not be empty');
        }
        $this->databasePath = trim($databasePath);
        IoHelper::normalizeDirectory($this->databasePath);

        $this->storePath = $this->databasePath . $this->storeName;

        if (count($schema) === 0) {
            $this->checkStore($storeName);
        }

        $this->indexesPath = $this->databasePath . $this->storeName . 'indexes/';

        $this->setConfigurationAndSchema($configuration, $schema);

        $this->createDatabasePath();

        if (count($schema) > 0) {
            $this->createStore($configuration, $schema);
        }
    }

    protected function checkStore($storeName)
    {
        if (!IoHelper::checkFolder($this->storePath)) {
            throw new InvalidStoreException('Store ' . $storeName . ' does not exist. Please provide configuration and schema to create new store');
        }

        return true;
    }

    public function getSchemaMetaData($relations = false)
    {
        $schemaArr = $this->getStoreSchema();

        if ($relations) {
            $rmd = [];
            $rmd['dataTypes'] = [];
            $rmd['number'] = [];
            $rmd['columns'] = [];
            $rmd['columnSize'] = [];
            $rmd['columnUnique'] = [];
            $rmd['storeRelations'] = [];

            if (isset($schemaArr['properties'])) {
                foreach ($schemaArr['properties'] as $column => $property) {
                    if (isset($property['type'][1]) && $property['type'][1] === 'array') {
                        if (isset($property['relation'])) {
                            $relation = explode('|', $property['relation']);

                            if (count($relation) > 0 && isset($relation[2])) {
                                try {
                                    $relationsStore = new Store($relation[2], $this->databasePath);

                                    $rmd = array_replace_recursive($rmd, $relationsStore->getSchemaMetaData());

                                    $rmd['storeRelations'][$column] = [];
                                    $rmd['storeRelations'][$column] = $relationsStore->getSchemaMetaData();
                                    $rmd['storeRelations'][$column]['relationStore'] = $relation[2];
                                } catch (\Exception $e) {
                                    //
                                }
                            }
                        }
                    }
                }
            }

            return $rmd;
        } else {
            $md = [];
            $md['dataTypes'] = [];
            $md['number'] = [];
            $md['columns'] = [];
            $md['columnSize'] = [];

            if (isset($schemaArr['properties'])) {
                foreach ($schemaArr['properties'] as $column => $property) {
                    if (isset($property['type'][1]) && $property['type'][1] === 'array') {
                        continue;
                    }

                    if (!is_array($property['type'])) {
                        $md['dataTypes'][$column] = $property['type'];
                    } else {
                        if (!in_array('null', $property['type'])) {
                            array_push($md['required'], $column);
                        }

                        foreach ($property['type'] as $type) {
                            if ($type !== 'null') {
                                $md['dataTypes'][$column] = $type;
                            }
                        }
                    }

                    if ($md['dataTypes'][$column] === 'integer' || $md['dataTypes'][$column] === 'number') {
                        $md['number'][$column] = true;
                    }

                    $md['columns'][$column] = $column;

                    if (isset($property['maxLength'])) {
                        $md['columnSize'][$column] = $property['maxLength'];
                    } else {
                        $md['columnSize'][$column] = 0;
                    }
                }
            }

            $md['columnUnique'] = $this->uniqueFields;

            return $md;
        }
    }

    public function getStoreSchema()
    {
        if (is_string($this->storeSchema)) {
            return json_decode($this->storeSchema, true);
        }

        return $this->storeSchema;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function createStore(array $configuration = [], array $schema = [])
    {
        IoHelper::createFolder($this->storePath, $this->folderPermissions);
        IoHelper::createFolder($this->storePath . 'cache', $this->folderPermissions);
        IoHelper::createFolder($this->storePath . 'indexes', $this->folderPermissions);
        IoHelper::createFolder($this->storePath . self::dataDirectory, $this->folderPermissions);

        if (!file_exists($this->storePath . '_cnt.sdb')) {
            IoHelper::writeContentToFile($this->storePath . '_cnt.sdb', '{"totalEntries":0,"lastId":0}');
        }

        if (!file_exists($this->storePath . 'config.json') ||
            count($configuration) > 0
        ) {
            IoHelper::writeContentToFile($this->storePath . 'config.json', json_encode($this->storeConfiguration));
        }

        if (!file_exists($this->storePath . 'schema.json') ||
            count($schema) > 0
        ) {
            if (is_array($this->storeSchema)) {
                $this->storeSchema = json_encode($this->storeSchema);
            }

            IoHelper::writeContentToFile($this->storePath . 'schema.json', $this->storeSchema);
        }
    }

    public function changeStore(string $storeName, string $databasePath = null, array $configuration = []): Store
    {
        if (empty($databasePath)) {
            $databasePath = $this->getDatabasePath();
        }

        $this->__construct($storeName, $databasePath, $configuration);

        return $this;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    public function deleteStore(): bool
    {
        return IoHelper::deleteFolder($this->storePath);
    }

    public function getLastInsertedId(): int
    {
        if (!file_exists($this->storePath . '_cnt.sdb')) {
            throw new IOException("File " . $this->storePath . '_cnt.sdb' . " does not exist.");
        }

        $counters = IoHelper::getFileContent($this->storePath . '_cnt.sdb');
        $counters = json_decode($counters, true);

        if (!isset($counters['lastId'])) {
            return 0;
        }

        return (int) $counters['lastId'];
    }

    public function getStorePath(): string
    {
        return $this->storePath;
    }

    public function findAll(array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder();

        if (!is_null($orderBy)) {
            $qb->orderBy($orderBy);
        }

        if (!is_null($limit)) {
            $qb->limit($limit);
        }

        if (!is_null($offset)) {
            $qb->skip($offset);
        }

        $this->data = $qb->getQuery()->fetch();

        return $this->data;
    }

    public function findById($id, $getRelations = false, $relationsConditions = false)
    {
        $id = $this->checkAndStripId($id);

        try {
            $content = IoHelper::getFileContent($this->getDataPath() . "$id.json");
        } catch (Exception $exception) {
            return null;
        }

        $data = @json_decode($content, true);

        if ($getRelations) {
            $data = $this->getRelations($data, $relationsConditions);
        }

        $this->data = $data;

        return $data;
    }

    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder();

        $qb->where($criteria);

        if ($orderBy !== null)  {
            $qb->orderBy($orderBy);
        }
        if ($limit !== null)  {
            $qb->limit($limit);
        }
        if ($offset !== null)  {
            $qb->skip($offset);
        }

        $this->data = $qb->getQuery()->fetch();

        return $this->data;
    }

    public function findOneBy(array $criteria, $getRelations = false, $relationsConditions = false)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($criteria);

        $result = $qb->getQuery()->first();

        if ($getRelations) {
            $result = $this->getRelations($result, $relationsConditions);
        }

        $this->data = (!empty($result)) ? $result : null;

        return $this->data;
    }

    public function insert(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data found to insert in the store');
        }

        $data = $this->writeNewDocumentToStore($data);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = $data;

        return $this->data;
    }

    public function insertMany(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data found to insert in the store');
        }

        $results = [];
        foreach ($data as $document) {
            $results[] = $this->writeNewDocumentToStore($document);
        }

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = $results;

        return $this->data;
    }

    public function updateOrInsert(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException("No document to update or insert.");
        }

        if (!array_key_exists($this->primaryKey, $data)) {
            $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
        } else {
            $data[$this->primaryKey] = $this->checkAndStripId($data[$this->primaryKey]);

            $current = $this->findById((int) $data[$this->primaryKey]);

            if ($autoGenerateIdOnInsert && $current === null) {
                $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
            } else {
                foreach ($current as $currentKey => $currentValue) {
                    if (!isset($data[$currentKey])) {
                        $data[$currentKey] = $currentValue;
                    }
                }
            }
        }

        $data = $this->validateData($data);

        $dataJSON = @json_encode($data);

        if ($dataJSON === false) {
            $this->decreaseCounter();

            throw new JsonException('Unable to encode the data array,
                                    please provide a valid PHP associative array');
        }

        IoHelper::writeContentToFile($this->getDataPath() . $data[$this->primaryKey] . '.json', $dataJSON, true, $this);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = $data;

        return $this->data;
    }

    public function updateOrInsertMany(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data))  {
            throw new InvalidArgumentException("No documents to update or insert.");
        }

        // Check if all documents have the primary key before updating or inserting any
        foreach ($data as $key => $document) {

            $document = $this->validateData($document);

            if (!is_array($document))  {
                throw new InvalidArgumentException('Documents have to be an arrays.');
            }

            if (!array_key_exists($this->primaryKey, $document))  {
                $document[$this->primaryKey] = $this->increaseCounterAndGetNextId();
            } else {
                $document[$this->primaryKey] = $this->checkAndStripId($document[$this->primaryKey]);

                $current = $this->findById((int) $document[$this->primaryKey]);

                if ($autoGenerateIdOnInsert && $current === null) {
                    $document[$this->primaryKey] = $this->increaseCounterAndGetNextId();
                } else {
                    foreach ($current as $currentKey => $currentValue) {
                        if (!isset($document[$currentKey])) {
                            $document[$currentKey] = $currentValue;
                        }
                    }
                }
            }

            $documentJSON = @json_encode($document);

            if ($documentJSON === false) {
                $this->decreaseCounter();

                throw new JsonException('Unable to encode the data array,
                                        please provide a valid PHP associative array');
            }

            IoHelper::writeContentToFile($this->getDataPath() . $document[$this->primaryKey] . '.json', $documentJSON, true, $this);
        }

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = $data;

        return $this->data;
    }

    public function update(array $data): array
    {
        if (empty($data))  {
            throw new InvalidArgumentException("No documents to update.");
        }

        if (!array_key_exists($this->primaryKey, $data))  {
            throw new InvalidArgumentException("Documents have to have the primary key \"$this->primaryKey\".");
        }

        $current = $this->findById((int) $data[$this->primaryKey]);

        foreach ($current as $currentKey => $currentValue) {
            if (!array_key_exists($currentKey, $data)) {
                $data[$currentKey] = $currentValue;
            }
        }

        $data = $this->validateData($data);

        if (!is_array($data))  {
            throw new InvalidArgumentException('Documents have to be an arrays.');
        }


        $data[$this->primaryKey] = $this->checkAndStripId($data[$this->primaryKey]);

        $dataJSON = @json_encode($data);

        if ($dataJSON === false) {
            throw new JsonException('Unable to encode the data array,
                                    please provide a valid PHP associative array');
        }

        if (!file_exists($this->getDataPath() . $data[$this->primaryKey] . '.json')) {
            return false;
        }

        IoHelper::writeContentToFile($this->getDataPath() . $data[$this->primaryKey] . '.json', $dataJSON, true, $this);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();


        $this->data = $data;

        return $this->data;
    }

    public function updateById($id, array $data)
    {
        $id = $this->checkAndStripId($id);

        $filePath = $this->getDataPath() . "$id.json";

        if (!file_exists($filePath)) {
            return false;
        }

        if (array_key_exists($this->primaryKey, $data))  {
            throw new InvalidArgumentException("You can not update the primary key \"$this->primaryKey\" of documents.");
        }

        $current = $this->findById((int) $id);

        foreach ($current as $currentKey => $currentValue) {
            if (!isset($data[$currentKey])) {
                $data[$currentKey] = $currentValue;
            }
        }

        $data = $this->validateData($data);

        $content = IoHelper::updateFileContent(
            $filePath,
            function($content) use ($filePath, $data) {
                $content = @json_decode($content, true);

                if (!is_array($content)) {
                    throw new JsonException("Could not decode content of \"$filePath\" with json_decode.");
                }

                foreach ($data as $key => $value) {
                    NestedHelper::updateNestedValue($key, $content, $value);
                }

                return json_encode($content);
            }
        );

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = $data;

        return $this->data;
    }

    public function deleteBy(array $criteria, $deleteRelated = true, int $returnOption = Query::DELETE_RETURN_BOOL)
    {
        $query = $this->createQueryBuilder()->where($criteria)->getQuery();

        $query->getCache()->deleteAllWithNoLifetime();

        return $query->delete($returnOption);
    }

    public function deleteById($id, $deleteRelated = true, $relationsConditions = false): bool
    {
        $id = $this->checkAndStripId($id);

        if ($deleteRelated && !$this->deleteRelated($id, $relationsConditions)) {
            return false;
        } else {
            $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

            return (!file_exists($this->getDataPath() . "$id.json") || true === @unlink($this->getDataPath() . "$id.json"));
        }
    }

    protected function deleteRelated($id, $relationsConditions = false)
    {
        $data = $this->findById((int) $id);

        if (!$data) {
            throw new InvalidArgumentException('Record with ID' . $id . ' not found!');
        }

        if (is_string($this->storeSchema)) {
            $schema = json_decode($this->storeSchema, true);
        }

        if (isset($schema['properties']) && count($schema['properties']) > 0) {
            foreach ($schema['properties'] as $propertyKey => $property) {
                if (!is_array($property['type'])) {
                    continue;
                }

                if (in_array('array', $property['type']) && isset($property['relation'])) {
                    $relation = explode('|', $property['relation']);

                    if (count($relation) > 0) {
                        if ($relation[1] === 'hasOne' || $relation[1] === 'hasMany') {
                            if ((in_array('hasParams', $relation) && $relationsConditions && count($relationsConditions) === 0) ||
                                (in_array('hasParams', $relation) && $relationsConditions && count($relationsConditions) > 0 && !isset($relationsConditions[$relation[0]]))
                            ) {
                                throw new InvalidArgumentException('Model has params(conditions) set for ' . $relation[0] . '. Please set ffRelationsConditions. Please refer to model file.');
                            }

                            if (isset($relation[4])) {
                                if (isset($relationsConditions[$relation[0]])) {
                                    try {
                                        $store = new Store($relation[2], $this->databasePath);

                                        $storeDataArr = $store->findBy($relationsConditions[$relation[0]]);

                                        if ($storeDataArr && is_array($storeDataArr) && count($storeDataArr) > 0) {
                                            foreach ($storeDataArr as $storeData) {
                                                if (isset($storeData['id'])) {
                                                    $store->deleteById((int) $storeData['id'], false);
                                                }
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        continue;
                                    }
                                } else {
                                    $fields = explode(':', $relation[4]);

                                    if (count($fields) > 0 && count($fields) % 2 == 0) {
                                        $fieldsArr = Arr::chunk($fields, 2);
                                        $criteria = [];

                                        foreach ($fieldsArr as $fieldArr) {
                                            array_push($criteria, [$fieldArr[1], '=', $data[$fieldArr[0]]]);
                                        }

                                        try {
                                            $store = new Store($relation[2], $this->databasePath);

                                            $storeDataArr = $store->findBy($criteria);
                                            if ($storeDataArr && is_array($storeDataArr) && count($storeDataArr) > 0) {
                                                foreach ($storeDataArr as $storeData) {
                                                    if (isset($storeData['id'])) {
                                                        $store->deleteById((int) $storeData['id'], false);
                                                    }
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            continue;
                                        }
                                    }
                                }
                            }
                        } else if ($relation[1] === 'hasOneThrough' || $relation[1] === 'hasManyThrough') {
                            if (isset($relation[2]) && isset($relation[3])) {
                                $relation[2] = explode('+', $relation[2]);
                                $relation[3] = explode('+', $relation[3]);
                            }

                            if (isset($relation[2][2]) && isset($relation[3][2])) {
                                $intermediateFields = explode(':', $relation[2][2]);
                                $fields = explode(':', $relation[3][2]);
                            }

                            if ((count($intermediateFields) > 0 && count($intermediateFields) % 2 == 0) &&
                                (count($fields) > 0 && count($fields) % 2 == 0)
                            ) {
                                $fieldsArr = Arr::chunk($intermediateFields, 2);
                                $criteria = [];

                                if (count($fieldsArr) === 1) {
                                    foreach ($fieldsArr as $fieldArr) {
                                        array_push($criteria, [$fieldArr[1], '=', $data[$fieldArr[0]]]);
                                    }
                                } else {
                                    foreach ($fieldsArr as $fieldArrKey => $fieldArr) {
                                        array_push($criteria, [$fieldArr[$fieldArrKey], '=', $data[$fieldArr[$fieldArrKey]]]);
                                    }
                                }

                                try {
                                    $store = new Store($relation[2][0], $this->databasePath);

                                    $storeData = $store->findOneBy($criteria);

                                    $fieldsArr = Arr::chunk($fields, 2);
                                    $criteria = [];

                                    if ($storeData && count($storeData) > 0) {
                                        if (count($fieldsArr) === 1) {
                                            foreach ($fieldsArr as $fieldArr) {
                                                array_push($criteria, [$fieldArr[1], '=', $storeData[$fieldArr[0]]]);
                                            }
                                        } else {
                                            foreach ($fieldsArr as $fieldArrKey => $fieldArr) {
                                                array_push($criteria, [$fieldArr[$fieldArrKey], '=', $storeData[$fieldArr[$fieldArrKey]]]);
                                            }
                                        }
                                    }

                                    if (count($criteria) > 0) {
                                        $store = new Store($relation[3][0], $this->databasePath);

                                        $storeDataArr = $store->findBy($criteria);

                                        if ($storeDataArr && is_array($storeDataArr) && count($storeDataArr) > 0) {
                                            foreach ($storeDataArr as $storeData) {
                                                if (isset($storeData['id'])) {
                                                    $store->deleteById((int) $storeData['id'], false);
                                                }
                                            }
                                        }
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function removeFieldsById($id, array $fieldsToRemove)
    {
        $id = $this->checkAndStripId($id);
        $filePath = $this->getDataPath() . "$id.json";

        if (in_array($this->primaryKey, $fieldsToRemove, false))  {
            throw new InvalidArgumentException("You can not remove the primary key \"$this->primaryKey\" of documents.");
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $content = IoHelper::updateFileContent(
            $filePath,
            function($content) use ($filePath, $fieldsToRemove) {
                $content = @json_decode($content, true);

                if (!is_array($content)) {
                    throw new JsonException("Could not decode content of \"$filePath\" with json_decode.");
                }

                foreach ($fieldsToRemove as $fieldToRemove){
                    NestedHelper::removeNestedField($content, $fieldToRemove);
                }

                return $content;
            }
        );

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        $this->data = json_decode($content, true);

        return $this->data;
    }

    public function search(array $fields, string $query, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder();

        $qb->search($fields, $query);

        if ($orderBy !== null)  {
            $qb->orderBy($orderBy);
        }

        if ($limit !== null)  {
            $qb->limit($limit);
        }

        if ($offset !== null)  {
            $qb->skip($offset);
        }

        $this->data = $qb->getQuery()->fetch();

        return $this->data;
    }

    public function getRelations($data, $relationsConditions = false)
    {
        if (count($data) === 0) {
            return $data;
        }

        if (is_string($this->storeSchema)) {
            $schema = json_decode($this->storeSchema, true);
        }

        if (isset($schema['properties']) && count($schema['properties']) > 0) {
            foreach ($schema['properties'] as $propertyKey => $property) {
                if (!is_array($property['type'])) {
                    continue;
                }

                if (in_array('array', $property['type']) && isset($property['relation'])) {
                    $relation = explode('|', $property['relation']);

                    if (count($relation) > 0) {
                        if ($relation[1] === 'hasOne' || $relation[1] === 'hasMany') {

                            if ((in_array('hasParams', $relation) && $relationsConditions && count($relationsConditions) === 0) ||
                                (in_array('hasParams', $relation) && $relationsConditions && count($relationsConditions) > 0 && !isset($relationsConditions[$relation[0]]))
                            ) {
                                throw new InvalidArgumentException('Model has params(conditions) set for ' . $relation[0] . '. Please set ffRelationsConditions. Please refer to model file.');
                            }

                            if (isset($relation[4])) {
                                if (isset($relationsConditions[$relation[0]])) {
                                    try {
                                        $store = new Store($relation[2], $this->databasePath);

                                        $storeData = $store->findBy($relationsConditions[$relation[0]]);

                                        if ($storeData && count($storeData) > 0) {
                                            if ($relation[1] === 'hasOne') {
                                                $data[$relation[0]] = $storeData[0];
                                            } else if ($relation[1] === 'hasMany') {
                                                $data[$relation[0]] = $storeData;
                                            }
                                        } else {
                                            $data[$relation[0]] = null;
                                        }
                                    } catch (\Exception $e) {
                                        continue;
                                    }
                                } else {
                                    $fields = explode(':', $relation[4]);

                                    if (count($fields) > 0 && count($fields) % 2 == 0) {
                                        $fieldsArr = Arr::chunk($fields, 2);
                                        $criteria = [];

                                        foreach ($fieldsArr as $fieldArr) {
                                            array_push($criteria, [$fieldArr[1], '=', $data[$fieldArr[0]]]);
                                        }

                                        try {
                                            $store = new Store($relation[2], $this->databasePath);

                                            $storeData = $store->findBy($criteria);

                                            if ($storeData && count($storeData) > 0) {
                                                if ($relation[1] === 'hasOne') {
                                                    $data[$relation[0]] = $storeData[0];
                                                } else if ($relation[1] === 'hasMany') {
                                                    $data[$relation[0]] = $storeData;
                                                }
                                            } else {
                                                $data[$relation[0]] = null;
                                            }
                                        } catch (\Exception $e) {
                                            continue;
                                        }
                                    }
                                }
                            }
                        } else if ($relation[1] === 'hasOneThrough' || $relation[1] === 'hasManyThrough') {
                            if (isset($relation[2]) && isset($relation[3])) {
                                $relation[2] = explode('+', $relation[2]);
                                $relation[3] = explode('+', $relation[3]);
                            }

                            if (isset($relation[2][2]) && isset($relation[3][2])) {
                                $intermediateFields = explode(':', $relation[2][2]);
                                $fields = explode(':', $relation[3][2]);
                            }

                            if ((count($intermediateFields) > 0 && count($intermediateFields) % 2 == 0) &&
                                (count($fields) > 0 && count($fields) % 2 == 0)
                            ) {
                                $fieldsArr = Arr::chunk($intermediateFields, 2);
                                $criteria = [];

                                if (count($fieldsArr) === 1) {
                                    foreach ($fieldsArr as $fieldArr) {
                                        array_push($criteria, [$fieldArr[1], '=', $data[$fieldArr[0]]]);
                                    }
                                } else {
                                    foreach ($fieldsArr as $fieldArrKey => $fieldArr) {
                                        array_push($criteria, [$fieldArr[$fieldArrKey], '=', $data[$fieldArr[$fieldArrKey]]]);
                                    }
                                }

                                try {
                                    $store = new Store($relation[2][0], $this->databasePath);

                                    $storeData = $store->findOneBy($criteria);

                                    $fieldsArr = Arr::chunk($fields, 2);
                                    $criteria = [];

                                    if ($storeData && count($storeData) > 0) {
                                        if (count($fieldsArr) === 1) {
                                            foreach ($fieldsArr as $fieldArr) {
                                                array_push($criteria, [$fieldArr[1], '=', $storeData[$fieldArr[0]]]);
                                            }
                                        } else {
                                            foreach ($fieldsArr as $fieldArrKey => $fieldArr) {
                                                array_push($criteria, [$fieldArr[$fieldArrKey], '=', $storeData[$fieldArr[$fieldArrKey]]]);
                                            }
                                        }
                                    }

                                    if (count($criteria) > 0) {
                                        $store = new Store($relation[3][0], $this->databasePath);

                                        $storeData = $store->findBy($criteria);

                                        if ($storeData && count($storeData) > 0) {
                                            if ($relation[1] === 'hasOneThrough') {
                                                $data[$relation[0]] = $storeData[0];
                                            } else if ($relation[1] === 'hasManyThrough') {
                                                $data[$relation[0]] = $storeData;
                                            }
                                        } else {
                                            $data[$relation[0]] = null;
                                        }
                                    } else {
                                        $data[$relation[0]] = null;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function count($recount = false): int
    {
        if ($this->useCache === true) {
            $cacheTokenArray = ["count" => true];

            $cache = new Cache($this->storePath, $cacheTokenArray, null);

            $cacheValue = $cache->get();

            if (is_array($cacheValue) && array_key_exists("count", $cacheValue)) {
                return $cacheValue["count"];
            }
        }

        $counters = IoHelper::countFolderContent($this->getDataPath(), $recount);

        if ($recount) {
            $this->updateCounters($counters);
        }

        $value = [
            "count" => $counters['totalEntries']
        ];

        if (isset($cache))  {
            $cache->set($value);
        }

        return $value["count"];
    }

    public function getSearchOptions(): array
    {
        return $this->searchOptions;
    }

    public function getUseCache(): bool
    {
        return $this->useCache;
    }

    public function getDefaultCacheLifetime()
    {
        return $this->defaultCacheLifetime;
    }

    protected function createDatabasePath()
    {
        IoHelper::createFolder($this->databasePath, $this->folderPermissions);
    }

    protected function setConfigurationAndSchema(array $configuration = [], array $schema = [])
    {
        if (count($configuration) === 0) {
            if (file_exists($this->storePath . 'config.json')) {
                $configuration = IoHelper::getFileContent($this->storePath . 'config.json');
                $configuration = json_decode($configuration, true);
            }
        }

        if (count($schema) === 0) {
            if (file_exists($this->storePath . 'schema.json')) {
                $schema = IoHelper::getFileContent($this->storePath . 'schema.json');
                $schema = json_decode($schema, true);
            }

            if (count($schema) > 0) {
                $this->storeSchema = json_encode($schema);
            }
        } else {
            $this->storeSchema = json_encode($schema);
        }

        if (array_key_exists("indexing", $configuration)) {
            if (!is_bool($configuration["indexing"])) {
                throw new InvalidConfigurationException("indexing has to be boolean");
            }

            $this->indexing = $configuration["indexing"];
        }

        if (array_key_exists("minIndexChars", $configuration)) {
            if (!is_int($configuration["minIndexChars"])) {
                throw new InvalidConfigurationException("minIndexChars has to be an integer");
            }

            $this->minIndexChars = $configuration["minIndexChars"];
        }

        if (array_key_exists("indexes", $configuration)) {
            if (!is_array($configuration["indexes"])) {
                throw new InvalidConfigurationException("indexes has to be an array");
            }

            $this->indexes = $configuration["indexes"];
        }

        if (array_key_exists("auto_cache", $configuration)) {
            if (!is_bool($configuration["auto_cache"])) {
                throw new InvalidConfigurationException("auto_cache has to be boolean");
            }

            $this->useCache = $configuration["auto_cache"];
        }

        if (array_key_exists("cache_lifetime", $configuration)) {
            if (!is_int($configuration["cache_lifetime"]) && !is_null($configuration["cache_lifetime"])){
                throw new InvalidConfigurationException("cache_lifetime has to be null or int");
            }

            $this->defaultCacheLifetime = $configuration["cache_lifetime"];
        }

        if (array_key_exists("primary_key", $configuration)) {
            if (!is_string($configuration["primary_key"])) {
                throw new InvalidConfigurationException("primary key has to be a string");
            }

            $this->primaryKey = $configuration["primary_key"];
        }

        if (array_key_exists("search", $configuration)) {
            if (array_key_exists("min_length", $configuration["search"])) {
                if (!is_int($configuration["search"]["min_length"]) || $configuration["search"]["min_length"] <= 0) {
                    throw new InvalidConfigurationException("min length for searching has to be an int >= 0");
                }

                $this->searchOptions["minLength"] = $configuration["search"]["min_length"];
            }

            if (array_key_exists("mode", $configuration["search"])) {
                if (!is_string($configuration["search"]["mode"]) ||
                    !in_array(strtolower(trim($configuration["search"]["mode"])), ["and", "or"])
                ) {
                    throw new InvalidConfigurationException("search mode can just be \"and\" or \"or\"");
                }

                $this->searchOptions["mode"] = strtolower(trim($configuration["search"]["mode"]));
            }

            if (array_key_exists("score_key", $configuration["search"])) {
                if (!is_string($configuration["search"]["score_key"]) &&
                    !is_null($configuration["search"]["score_key"])
                ) {
                    throw new InvalidConfigurationException("search score key for search has to be a not empty string or null");
                }

                $this->searchOptions["scoreKey"] = $configuration["search"]["score_key"];
            }

            if (array_key_exists("algorithm", $configuration["search"])) {
                if (!in_array($configuration["search"]["algorithm"], Query::SEARCH_ALGORITHM, true)) {
                    $configuration["search"]["algorithm"] = implode(', ', $configuration["search"]["algorithm"]);

                    throw new InvalidConfigurationException(
                        'The search algorithm has to be one of the following integer values (' . $configuration['search']['algorithm'] . ')'
                    );
                }

                $this->searchOptions["algorithm"] = $configuration["search"]["algorithm"];
            }
        }

        if (array_key_exists("uniqueFields", $configuration)) {
            if (!is_array($configuration["uniqueFields"])) {
                throw new InvalidConfigurationException("uniqueFields has to be an array");
            }

            $this->uniqueFields = $configuration["uniqueFields"];
        }

        if (array_key_exists("folder_permissions", $configuration)) {
            if (!is_int($configuration["folder_permissions"])) {
                throw new InvalidConfigurationException("folder_permissions has to be an integer (e.g. 0777)");
            }

            $this->folderPermissions = $configuration["folder_permissions"];
        }

        $this->storeConfiguration();
    }

    protected function storeConfiguration()
    {
        $this->storeConfiguration =
        [
            "auto_cache"            => $this->useCache,
            "cache_lifetime"        => $this->defaultCacheLifetime,
            "primary_key"           => $this->primaryKey,
            "search"                => [
                "min_length"            => $this->searchOptions["minLength"],
                "mode"                  => $this->searchOptions["mode"],
                "score_key"             => $this->searchOptions["scoreKey"],
                "algorithm"             => $this->searchOptions["algorithm"]
            ],
            "folder_permissions"    => $this->folderPermissions,
            "indexing"              => $this->indexing,
            "min_index_chars"       => $this->minIndexChars,
            "uniqueFields"          => $this->uniqueFields,
            "indexes"               => $this->indexes,
            "storePath"             => $this->storePath,
            "databasePath"          => $this->databasePath,
            "indexesPath"           => $this->indexesPath
        ];
    }

    public function getStoreConfiguration()
    {
        return $this->storeConfiguration;
    }

    protected function writeNewDocumentToStore(array $storeData): array
    {
        if (isset($storeData[$this->primaryKey])) {
            throw new IdNotAllowedException(
                "The $this->primaryKey\" index is reserved, please delete the $this->primaryKey key and try again"
            );
        }

        $storeData = $this->validateData($storeData);

        $id = $this->increaseCounterAndGetNextId();

        $storeData[$this->primaryKey] = $id;

        $storableJSON = @json_encode($storeData);

        if ($storableJSON === false) {
            $this->decreaseCounter();

            throw new JsonException('Unable to encode the data array,
                                    please provide a valid PHP associative array');
        }

        IoHelper::writeContentToFile($this->getDataPath() . "$id.json", $storableJSON, true, $this);

        return $storeData;
    }

    protected function validateData(array $data)
    {
        if (!isset($data['id']) && count($this->uniqueFields) > 0) {
            $criteria = [];

            foreach ($this->uniqueFields as $uniqueField) {
                if (isset($data[$uniqueField])) {
                    array_push($criteria, [$uniqueField, '=', $data[$uniqueField]]);
                }
            }

            if (count($criteria) > 0) {
                $found = $this->findOneBy($criteria);

                if ($found) {
                    throw new IOException("Duplicate entry found for field: $uniqueField. $uniqueField should be unique. Store: " . $this->storeName);
                }
            }
        }

        if ($this->storeSchema === null) {
            return true;
        }

        $data = $this->normalizeData($data);

        $validator = new Validator();

        $formats = $validator->parser()->getFormatResolver();

        $this->registerNewValidatorFormats($formats);

        $result = $validator->validate(Helper::toJSON($data), $this->storeSchema);

        if ($result->isValid()) {
            $data = $this->normalizeData($data, true);

            if (isset($data['id']) && $data['id'] == 0) {
                unset($data['id']);
            }

            return $data;
        }

        if ($result->hasError()) {
            $errors = 'Schema: ' . $result->error()->schema()->info()->data()->{'$id'} . '<br>';

            $errors .= $result->error()->__toString() . '. ';

            if ($result->error()->args() &&
                count($result->error()->args()) > 0
            ) {
                foreach ($result->error()->args() as $key => $arg) {
                    $args = $arg;

                    if (is_array($arg)) {
                        $args = join(',', $arg);
                    }

                    $errors = str_replace('{' . $key . '}', $args, $errors);
                }
            }

            if ($result->error()->subErrors() &&
                count($result->error()->subErrors()) > 0
            ) {
                foreach ($result->error()->subErrors() as $subError) {
                    $errors .= '<br>' . $subError->data()->path()[0] . ': ' . $subError->__toString() . '. ';

                    if (count($subError->args()) > 0) {
                        foreach ($subError->args() as $key => $arg) {
                            if ($key === 'expected') {
                                if (is_array($arg)) {
                                    $arg = join(' | ', $arg);
                                }

                                $errors = str_replace('{' . $key . '}', $arg, $errors);
                            } else {
                                $errors = str_replace('{' . $key . '}', $arg, $errors);
                            }
                        }
                    }
                }
            }

            throw new InvalidDataException($errors);
        }
    }

    protected function normalizeData(array $data, $jsonToArray = false): array
    {
        if (is_string($this->storeSchema)) {
            $schema = json_decode($this->storeSchema, true);
        }
        if (isset($schema['properties']) && count($schema['properties']) > 0) {
            if (!isset($data['id'])) {
                $data['id'] = 0;
            }

            foreach ($data as $dataKey => $dataValue) {
                if (!isset($schema['properties'][$dataKey])) {
                    unset($data[$dataKey]);
                }
            }

            foreach ($schema['properties'] as $propertyKey => $property) {
                if (!isset($data[$propertyKey])) {
                    $data[$propertyKey] = null;
                }

                if (isset($property['relation'])) {
                    unset($data[$propertyKey]);
                }

                if ($jsonToArray) {
                    if (array_key_exists('format', $property)) {
                        if ($property['format'] === 'json') {
                            if (is_string($data[$propertyKey])) {
                                $utils = new Utils();

                                if (!$utils->validateJson(['json' => $data[$propertyKey]])) {
                                    throw new \Exception($utils->packagesData->responseMessage);
                                }

                                $data[$propertyKey] = json_decode($data[$propertyKey], true);
                            }
                        }
                    }
                } else {
                    if (array_key_exists('format', $property)) {
                        if ($property['format'] === 'json') {
                            if (isset($data[$propertyKey]) && is_array($data[$propertyKey])) {
                                $data[$propertyKey] = json_encode($data[$propertyKey]);
                            }
                        }
                    }

                    if (isset($property['format']) && $property['format'] === 'date-time') {
                        if (in_array($propertyKey, $schema['required'])) {
                            if (!isset($data[$propertyKey])) {
                                $data[$propertyKey] = date('c');
                            }
                        }
                    }

                    if (array_key_exists('default', $property)) {
                        if (!isset($data[$propertyKey])) {
                            $data[$propertyKey] = $property['default'];
                        }
                    }

                    if (is_string($property['type'])) {
                        $property['type'] = [$property['type']];
                    }

                    foreach ($property['type'] as $type) {
                        if ($type === 'null') {
                            continue;
                        }

                        if ($type === 'boolean' || $type === 'integer') {
                            if (is_string($data[$propertyKey])) {
                                $data[$propertyKey] = (int) $data[$propertyKey];
                            }

                            if (is_int($data[$propertyKey]) && $type === 'boolean') {
                                $data[$propertyKey] = $data[$propertyKey] === 0 ? false : true;
                            }
                        }

                        if ($type === 'number') {
                            if (is_string($data[$propertyKey]) && $type === 'number') {
                                $data[$propertyKey] = (float) $data[$propertyKey];
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    protected function registerNewValidatorFormats($formats)
    {
        $jsonFormat = function($value): bool {
            if (is_array($value)) {
                return true;
            }

            if (is_string($value)) {
                $utils = new Utils();

                if (!$utils->validateJson(['json' => $value])) {
                    return false;
                }

                return true;
            }

            return false;
        };

        $formats->registerCallable('string', 'json', $jsonFormat);
    }

    protected function increaseCounterAndGetNextId(): int
    {
        if (!file_exists($this->storePath . '_cnt.sdb')) {
            throw new IOException("File " . $this->storePath . '_cnt.sdb' . " does not exist.");
        }

        $dataPath = $this->getDataPath();

        $counters = IoHelper::getFileContent($this->storePath . '_cnt.sdb');
        $counters = json_decode($counters, true);

        $newId = ((int) $counters['lastId']) + 1;
        while (file_exists($dataPath . "$newId.json") === true) {
            $newId++;
        }

        $counters['lastId'] = $newId;
        $counters['totalEntries'] = $counters['totalEntries'] + 1;

        $this->updateCounters($counters);

        return $newId;
    }

    protected function decreaseCounter()
    {
        $counters = IoHelper::getFileContent($this->storePath . '_cnt.sdb');
        $counters = json_decode($counters, true);

        $previousId = ((int) $counters['lastId']) - 1;

        $counters['lastId'] = $previousId;
        $counters['totalEntries'] = $counters['totalEntries'] - 1;

        $this->updateCounters($counters);

        return $previousId;
    }

    protected function updateCounters($counters)
    {
        $updateCounters = IoHelper::updateFileContent(
            $this->storePath . '_cnt.sdb',
            function () use ($counters) {
                return json_encode($counters);
            }
        );

        if (!$updateCounters) {
            throw new IOException("File " . $this->storePath . '_cnt.sdb' . ". Could not update Id.");
        }

        return true;
    }

    protected function checkAndStripId($id): int
    {
        if (!is_string($id) && !is_int($id)) {
            throw new InvalidArgumentException("The id of the document has to be an integer or string");
        }

        if (is_string($id)) {
            $id = IoHelper::secureStringForFileAccess($id);
        }

        if (!is_numeric($id)) {
            throw new InvalidArgumentException("The id of the document has to be numeric");
        }

        return (int) $id;
    }

    public function getDataPath(): string
    {
        return $this->storePath . self::dataDirectory;
    }

    public function getDatabasePath(): string
    {
        return $this->databasePath;
    }

    public function reIndexStore()
    {
        (new IndexHandler($this->storeConfiguration))->reIndex();

        return $this;
    }
}