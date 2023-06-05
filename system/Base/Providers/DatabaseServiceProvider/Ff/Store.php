<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff;

use Exception;
use Opis\JsonSchema\Validator;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IndexHandler;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\NestedHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IdNotAllowedException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidConfigurationException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidDataException;
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

        $this->indexesPath = $this->databasePath . $this->storeName . 'indexes/';

        $this->setConfiguration($configuration, $schema);

        $this->createDatabasePath();

        $this->createStore($configuration, $schema);
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function createStore(array $configuration = [], array $schema = [])
    {
        IoHelper::createFolder($this->storePath, $this->folderPermissions);
        IoHelper::createFolder($this->storePath . 'cache', $this->folderPermissions);
        IoHelper::createFolder($this->storePath . 'indexes', $this->folderPermissions);
        IoHelper::createFolder($this->storePath . self::dataDirectory, $this->folderPermissions);

        if (!file_exists($this->storePath . '_cnt.sdb')) {
            IoHelper::writeContentToFile($this->storePath . '_cnt.sdb', '0');
        }

        if (!file_exists($this->storePath . 'config.json') ||
            count($configuration) > 0
        ) {
            IoHelper::writeContentToFile($this->storePath . 'config.json', json_encode($this->storeConfiguration));
        }

        if (!file_exists($this->storePath . 'schema.json') ||
            count($schema) > 0
        ) {
            IoHelper::writeContentToFile($this->storePath . 'schema.json', json_encode($this->storeSchema));
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
        return (int) IoHelper::getFileContent($this->storePath . '_cnt.sdb');
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

        return $qb->getQuery()->fetch();
    }

    public function findById($id)
    {
        $id = $this->checkAndStripId($id);

        try {
            $content = IoHelper::getFileContent($this->getDataPath() . "$id.json");
        } catch (Exception $exception) {
            return null;
        }

        return @json_decode($content, true);
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

        return $qb->getQuery()->fetch();
    }

    public function findOneBy(array $criteria)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($criteria);

        $result = $qb->getQuery()->first();

        return (!empty($result)) ? $result : null;
    }

    public function insert(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data found to insert in the store');
        }

        $data = $this->writeNewDocumentToStore($data);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return $data;
    }

    public function updateOrInsert(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException("No document to update or insert.");
        }

        $this->validateData($data);

        if (!array_key_exists($this->primaryKey, $data)) {
            $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
        } else {
            $data[$this->primaryKey] = $this->checkAndStripId($data[$this->primaryKey]);

            if ($autoGenerateIdOnInsert && $this->findById($data[$this->primaryKey]) === null) {
                $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
            }
        }

        $dataJSON = @json_encode($data);

        if ($dataJSON === false) {
            $this->decreaseCounter();

            throw new JsonException('Unable to encode the data array,
                                    please provide a valid PHP associative array');
        }

        IoHelper::writeContentToFile($this->getDataPath() . $data[$this->primaryKey] . '.json', $dataJSON, true, $this);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return $data;
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

        return $results;
    }

    public function updateOrInsertMany(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data))  {
            throw new InvalidArgumentException("No documents to update or insert.");
        }

        // Check if all documents have the primary key before updating or inserting any
        foreach ($data as $key => $document) {
            $this->validateData($document);

            if (!is_array($document))  {
                throw new InvalidArgumentException('Documents have to be an arrays.');
            }

            if (!array_key_exists($this->primaryKey, $document))  {
                $document[$this->primaryKey] = $this->increaseCounterAndGetNextId();
            } else {
                $document[$this->primaryKey] = $this->checkAndStripId($document[$this->primaryKey]);

                if ($autoGenerateIdOnInsert && $this->findById($document[$this->primaryKey]) === null) {
                    $document[$this->primaryKey] = $this->increaseCounterAndGetNextId();
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

        return $data;
    }

    public function update(array $data): bool
    {
        if (empty($data))  {
            throw new InvalidArgumentException("No documents to update.");
        }

        // we can use this check to determine if multiple documents are given
        // because documents have to have at least the primary key.
        if (array_keys($data) !== range(0, (count($data) - 1))) {
            $data = [$data];
        }

        // Check if all documents exist and have the primary key before updating any
        foreach ($data as $key => $document) {
            $this->validateData($document);

            if (!is_array($document))  {
                throw new InvalidArgumentException('Documents have to be an arrays.');
            }

            if (!array_key_exists($this->primaryKey, $document))  {
                throw new InvalidArgumentException("Documents have to have the primary key \"$this->primaryKey\".");
            }

            $document[$this->primaryKey] = $this->checkAndStripId($document[$this->primaryKey]);

            $documentJSON = @json_encode($document);

            if ($documentJSON === false) {
                throw new JsonException('Unable to encode the data array,
                                        please provide a valid PHP associative array');
            }

            if (!file_exists($this->getDataPath() . $document[$this->primaryKey] . '.json')) {
                return false;
            }

            IoHelper::writeContentToFile($this->getDataPath() . $document[$this->primaryKey] . '.json', $documentJSON, true, $this);
        }

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return true;
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

        $this->validateData($data);

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

        return json_decode($content, true);
    }

    public function deleteBy(array $criteria, int $returnOption = Query::DELETE_RETURN_BOOL)
    {
        $query = $this->createQueryBuilder()->where($criteria)->getQuery();

        $query->getCache()->deleteAllWithNoLifetime();

        return $query->delete($returnOption);
    }

    public function deleteById($id): bool
    {
        $id = $this->checkAndStripId($id);

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return (!file_exists($this->getDataPath() . "$id.json") || true === @unlink($this->getDataPath() . "$id.json"));
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

        return json_decode($content, true);
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

        return $qb->getQuery()->fetch();
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function count(): int
    {
        if ($this->useCache === true) {
            $cacheTokenArray = ["count" => true];

            $cache = new Cache($this->storePath, $cacheTokenArray, null);

            $cacheValue = $cache->get();

            if (is_array($cacheValue) && array_key_exists("count", $cacheValue)) {
                return $cacheValue["count"];
            }
        }

        $value = [
            "count" => IoHelper::countFolderContent($this->getDataPath())
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

    protected function setConfiguration(array $configuration = [], array $schema = [])
    {
        $writeConfig = true;

        if (count($configuration) === 0) {
            if (file_exists($this->storePath . 'config.json')) {
                $configuration = IoHelper::getFileContent($this->storePath . 'config.json');
                $configuration = json_decode($configuration, true);
            }

            if (count($configuration) === 0) {
                $writeConfig = true;
            } else {
                $writeConfig = false;
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

        if ($writeConfig) {
            $this->storeConfiguration();
        }
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

        $this->validateData($storeData);

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
        if (count($this->uniqueFields) > 0) {
            foreach ($this->uniqueFields as $uniqueField) {
                if (isset($data[$uniqueField])) {
                    $found = $this->findOneBy([$uniqueField, '=', $data[$uniqueField]]);

                    if ($found) {
                        throw new IOException("Duplicate entry found for field: $uniqueField. $uniqueField should be unique.");
                    }
                }
            }
        }

        if ($this->storeSchema === null) {
            return true;
        }

        $validator = new Validator();

        $result = $validator->validate((object) $data, $this->storeSchema);

        if ($result->isValid()) {
            return true;
        }

        if ($result->hasError()) {
            $errors = $result->error()->__toString() . '. ';

            if ($result->error()->subErrors() && count($result->error()->subErrors()) > 0) {
                foreach ($result->error()->subErrors() as $subError) {
                    $errors .= $subError->__toString() . '. ';

                    if (count($subError->args()) > 0) {
                        foreach ($subError->args() as $key => $arg) {
                            $errors .= $key . ' ' . $arg . ' ';
                        }
                    }
                }
            }

            throw new InvalidDataException($errors);
        }
    }

    protected function increaseCounterAndGetNextId(): int
    {
        if (!file_exists($this->storePath . '_cnt.sdb')) {
            throw new IOException("File " . $this->storePath . '_cnt.sdb' . " does not exist.");
        }

        $dataPath = $this->getDataPath();

        return (int) IoHelper::updateFileContent(
            $this->storePath . '_cnt.sdb',
            function ($counter) use ($dataPath) {
                $newCounter = ((int) $counter) + 1;

                while (file_exists($dataPath . "$newCounter.json") === true) {
                    $newCounter++;
                }

                return (string) $newCounter;
            }
        );
    }

    protected function decreaseCounter()
    {
        $dataPath = $this->getDataPath();

        return (int) IoHelper::updateFileContent(
            $this->storePath . '_cnt.sdb',
            function ($counter) use ($dataPath) {
                $newCounter = ((int) $counter) - 1;

                return (string) $newCounter;
            }
        );
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