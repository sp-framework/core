<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff;

use Exception;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\NestedHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IdNotAllowedException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidConfigurationException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\JsonException;

class Store
{
    protected $root = __DIR__;
    protected $storeName = "";
    protected $storePath = "";
    protected $databasePath = "";

    protected $useCache = true;
    protected $defaultCacheLifetime = null;
    protected $indexing = false;
    protected $indexes = [];
    protected $minIndexChars = 3;
    protected $primaryKey = "id";
    protected $searchOptions = [
        "minLength" => 2,
        "scoreKey" => "searchScore",
        "mode" => "or",
        "algorithm" => Query::SEARCH_ALGORITHM["hits"]
    ];
    protected $folderPermissions = 0777;

    protected $schema;

    const dataDirectory = "data/";

    public function __construct(string $storeName, string $databasePath, array $configuration = [], $schema = null)
    {
        if (empty($storeName)) {
            throw new InvalidArgumentException('store name can not be empty');
        }
        $this->storeName = trim($storeName);

        if (empty($databasePath)) {
            throw new InvalidArgumentException('data directory can not be empty');
        }
        $this->databasePath = trim($databasePath);
        IoHelper::normalizeDirectory($this->databasePath);

        $this->setConfiguration($configuration);

        $this->createDatabasePath();

        $this->createStore();

        if ($schema) {
            $this->schema = $schema;
        }
    }

    public function getSchema()
    {
        return $this->schema;
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

    public function insert(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data found to insert in the store');
        }

        $data = $this->writeNewDocumentToStore($data);

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

    public function updateOrInsert(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException("No document to update or insert.");
        }

        if (!array_key_exists($this->primaryKey, $data)) {
            $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
        } else {
            $data[$this->primaryKey] = $this->checkAndStripId($data[$this->primaryKey]);

            if ($autoGenerateIdOnInsert && $this->findById($data[$this->primaryKey]) === null) {
                $data[$this->primaryKey] = $this->increaseCounterAndGetNextId();
            }
        }

        IoHelper::writeContentToFile($this->getDataPath() . $data[$this->primaryKey] . '.json', json_encode($data));

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return $data;
    }

    public function updateOrInsertMany(array $data, bool $autoGenerateIdOnInsert = true): array
    {
        if (empty($data))  {
            throw new InvalidArgumentException("No documents to update or insert.");
        }

        // Check if all documents have the primary key before updating or inserting any
        foreach ($data as $key => $document){
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

            $data[$key] = $document;
        }

        foreach ($data as $document) {
            IoHelper::writeContentToFile($this->getDataPath() . $document[$this->primaryKey] . '.json', json_encode($document));
        }

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return $data;
    }

    public function update(array $updatable): bool
    {
        if (empty($updatable))  {
            throw new InvalidArgumentException("No documents to update.");
        }

        // we can use this check to determine if multiple documents are given
        // because documents have to have at least the primary key.
        if (array_keys($updatable) !== range(0, (count($updatable) - 1))) {
            $updatable = [$updatable];
        }

        // Check if all documents exist and have the primary key before updating any
        foreach ($updatable as $key => $document){
            if (!is_array($document))  {
                throw new InvalidArgumentException('Documents have to be an arrays.');
            }

            if (!array_key_exists($this->primaryKey, $document))  {
                throw new InvalidArgumentException("Documents have to have the primary key \"$this->primaryKey\".");
            }

            $document[$this->primaryKey] = $this->checkAndStripId($document[$this->primaryKey]);

            $updatable[$key] = $document;

            if (!file_exists($this->getDataPath() . $document[$this->primaryKey] . '.json')) {
                return false;
            }
        }

        foreach ($updatable as $document) {
            IoHelper::writeContentToFile($this->getDataPath() . $document[$this->primaryKey] . '.json', json_encode($document));
        }

        $this->createQueryBuilder()->getQuery()->getCache()->deleteAllWithNoLifetime();

        return true;
    }

    public function updateById($id, array $updatable)
    {
        $id = $this->checkAndStripId($id);

        $filePath = $this->getDataPath() . "$id.json";

        if (array_key_exists($this->primaryKey, $updatable))  {
            throw new InvalidArgumentException("You can not update the primary key \"$this->primaryKey\" of documents.");
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $content = IoHelper::updateFileContent(
            $filePath,
            function($content) use ($filePath, $updatable) {
                $content = @json_decode($content, true);

                if (!is_array($content)) {
                    throw new JsonException("Could not decode content of \"$filePath\" with json_decode.");
                }

                foreach ($updatable as $key => $value){
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

    protected function createStore()
    {
        IoHelper::normalizeDirectory($this->storeName);

        $this->storePath = $this->databasePath . $this->storeName;

        IoHelper::createFolder($this->storePath, $this->folderPermissions);
        IoHelper::createFolder($this->storePath . 'cache', $this->folderPermissions);
        IoHelper::createFolder($this->storePath . self::dataDirectory, $this->folderPermissions);

        if (!file_exists($this->storePath . '_cnt.sdb')) {
            IoHelper::writeContentToFile($this->storePath . '_cnt.sdb', '0');
        }
    }

    protected function setConfiguration(array $configuration)
    {
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

        if (array_key_exists("folder_permissions", $configuration)) {
            if (!is_int($configuration["folder_permissions"])) {
                throw new InvalidConfigurationException("folder_permissions has to be an integer (e.g. 0777)");
            }

            $this->folderPermissions = $configuration["folder_permissions"];
        }
    }

    protected function writeNewDocumentToStore(array $storeData): array
    {
        if (isset($storeData[$this->primaryKey])) {
            throw new IdNotAllowedException(
                "The $this->primaryKey\" index is reserved by SleekDB, please delete the $this->primaryKey key and try again"
            );
        }

        $id = $this->increaseCounterAndGetNextId();

        $storeData[$this->primaryKey] = $id;

        $storableJSON = @json_encode($storeData);

        if ($storableJSON === false) {
            throw new JsonException('Unable to encode the data array,
                                    please provide a valid PHP associative array');
        }

        IoHelper::writeContentToFile($this->getDataPath() . "$id.json", $storableJSON);

        return $storeData;
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
}