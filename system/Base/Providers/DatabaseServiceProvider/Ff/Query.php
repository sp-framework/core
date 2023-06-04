<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff;

use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\CacheHandler;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\DocumentFinder;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\DocumentUpdater;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;

class Query
{
    const DELETE_RETURN_BOOL = 1;
    const DELETE_RETURN_RESULTS = 2;
    const DELETE_RETURN_COUNT = 3;

    const SEARCH_ALGORITHM = [
        "hits" => 1,
        "hits_prioritize" => 2,
        "prioritize" => 3,
        "prioritize_position" => 4,
    ];

    protected $cacheHandler;

    protected $documentFinder;

    protected $documentUpdater;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->cacheHandler = new CacheHandler($queryBuilder->getStore()->getStorePath(),
                                               $queryBuilder
                                              );

        $this->documentFinder = new DocumentFinder($queryBuilder->getStore()->getStorePath(),
                                                   $queryBuilder->getConditionProperties(),
                                                   $queryBuilder->getStore()->getPrimaryKey()
                                                  );

        $this->documentUpdater = new DocumentUpdater($queryBuilder->getStore()->getStorePath(),
                                                     $queryBuilder->getStore()->getPrimaryKey()
                                                    );
    }

    public function fetch(): array
    {
        return $this->getResults();
    }

    public function exists(): bool
    {
        return !empty($this->first());
    }

    public function first(): array
    {
        return $this->getResults(true);
    }

    public function update(array $updatable, bool $returnUpdatedDocuments = false)
    {
        if (empty($updatable)) {
            throw new InvalidArgumentException("You have to define what you want to update.");
        }

        $results = $this->documentFinder->findDocuments(false, false);

        $this->getCacheHandler()->deleteAllWithNoLifetime();

        return $this->documentUpdater->updateResults($results, $updatable, $returnUpdatedDocuments);
    }

    public function delete(int $returnOption = self::DELETE_RETURN_BOOL)
    {
        $results = $this->documentFinder->findDocuments(false, false);

        $this->getCacheHandler()->deleteAllWithNoLifetime();

        return $this->documentUpdater->deleteResults($results, $returnOption);
    }

    public function removeFields(array $fieldsToRemove)
    {
        if (empty($fieldsToRemove)) {
            throw new InvalidArgumentException("You have to define what fields you want to remove.");
        }

        $results = $this->documentFinder->findDocuments(false, false);

        $this->getCacheHandler()->deleteAllWithNoLifetime();

        return $this->documentUpdater->removeFields($results, $fieldsToRemove);
    }

    public function getCache(): Cache
    {
        return $this->getCacheHandler()->getCache();
    }

    protected function getResults(bool $getOneDocument = false): array
    {
        $results = $this->getCacheHandler()->getCacheContent($getOneDocument);

        if ($results !== null) {
            return $results;
        }

        $results = $this->documentFinder->findDocuments($getOneDocument, true);

        if ($getOneDocument === true && count($results) > 0) {
            list($item) = $results;
            $results = $item;
        }

        $this->getCacheHandler()->setCacheContent($results);

        return $results;
    }

    protected function getCacheHandler(): CacheHandler
    {
        return $this->cacheHandler;
    }
}