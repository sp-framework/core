<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup;

/**
 * Builds and applies schema tables and FlatFile stores, and handles indexing.
 */
class SchemaBuilder
{
    /**
     * Database connection adapter.
     *
     * @var mixed
     */
    protected mixed $db;

    /**
     * FlatFile database adapter.
     *
     * @var mixed
     */
    protected mixed $ff;

    /**
     * Setup post payload.
     *
     * @var array<string, mixed>
     */
    protected array $postData;

    /**
     * Database config credentials.
     *
     * @var array<string, mixed>
     */
    protected array $dbConfig;

    /**
     * Cleaner instance.
     *
     * @var Cleaner|null
     */
    protected ?Cleaner $cleaner = null;

    /**
     * Database provisioner instance.
     *
     * @var DatabaseProvisioner|null
     */
    protected ?DatabaseProvisioner $databaseProvisioner = null;

    /**
     * List of FlatFile stores queued for indexing.
     *
     * @var array<int, string>
     */
    protected array $storesToIndex = [];

    /**
     * SchemaBuilder constructor.
     *
     * @param mixed                    $db                  Database connection.
     * @param mixed                    $ff                  FlatFile connection.
     * @param array<string, mixed>     $postData            Setup post payload.
     * @param array<string, mixed>     $dbConfig            Database config.
     * @param Cleaner|null             $cleaner             Cleaner instance.
     * @param DatabaseProvisioner|null $databaseProvisioner Database provisioner instance.
     */
    public function __construct(
        mixed $db,
        mixed $ff,
        array $postData = [],
        array $dbConfig = [],
        ?Cleaner $cleaner = null,
        ?DatabaseProvisioner $databaseProvisioner = null
    ) {
        $this->db = $db;
        $this->ff = $ff;
        $this->postData = $postData;
        $this->dbConfig = $dbConfig;
        $this->cleaner = $cleaner;
        $this->databaseProvisioner = $databaseProvisioner;
    }

    /**
     * Builds and applies schema tables and FlatFile stores.
     *
     * @return bool True on success.
     */
    public function buildSchema(): bool
    {
        $dev = $this->postData['dev'] ?? false;
        $databases = (new Schema())->getSchema($dev);
        $dbType = $this->postData['databasetype'] ?? 'hybrid';

        if ($dbType !== 'ff' && $this->db) {
            $dbname = $this->dbConfig['db']['dbname'] ?? ($this->postData['dbname'] ?? 'sp');

            foreach ($databases as $tableName => $tableClass) {
                if (isset($tableClass['schema'])) {
                    if (method_exists($tableClass['schema'], 'columns') && method_exists($this->db, 'createTable')) {
                        $this->db->createTable($tableName, $dbname, $tableClass['schema']->columns());
                    }
                    if (method_exists($tableClass['schema'], 'indexes') && $this->databaseProvisioner) {
                        $this->databaseProvisioner->addIndex($tableName, $tableClass['schema']->indexes());
                    }
                }
            }
        }

        if ($dbType !== 'db' && $this->ff) {
            if ($this->cleaner) {
                $this->cleaner->cleanOldFfs();
            }

            foreach ($databases as $tableName => $tableClass) {
                if (!isset($tableClass['schema'])) {
                    continue;
                }

                if (isset($tableClass['model']) && is_object($tableClass['model']) && method_exists($tableClass['model'], 'getSource')) {
                    try {
                        $source = $tableClass['model']->getSource();
                        if (!empty($source)) {
                            $tableName = $source;
                        }
                    } catch (\Throwable $e) {
                        // Fall back to $tableName
                    }
                }

                $tableConfigParams = $tableClass['configParams'] ?? [];
                $config = $this->ff->generateConfig($tableName, $tableClass['schema'], $tableClass['model'] ?? null, $tableConfigParams);
                $schema = $this->ff->generateSchema($tableName, $tableClass['schema'], $tableClass['model'] ?? null);

                $store = $this->ff->store($tableName, $config, $schema, $this->ff);
                if (method_exists($store, 'deleteStore')) {
                    $store->deleteStore();
                }

                $this->ff->store($tableName, $config, $schema, $this->ff);

                if (method_exists($tableClass['schema'], 'indexes')) {
                    $this->storesToIndex[] = $tableName;
                }
            }
        }

        return true;
    }

    /**
     * Re-indexes FlatFile document stores.
     *
     * @return bool True on success.
     */
    public function performIndexing(): bool
    {
        $dbType = $this->postData['databasetype'] ?? 'hybrid';

        if ($dbType !== 'db' && count($this->storesToIndex) > 0 && $this->ff) {
            foreach ($this->storesToIndex as $storeToIndex) {
                $store = $this->ff->store($storeToIndex);
                if (method_exists($store, 'reIndexStore')) {
                    $store->reIndexStore();
                }
            }
        }

        return true;
    }

    /**
     * Gets stores queued for indexing.
     *
     * @return array<int, string>
     */
    public function getStoresToIndex(): array
    {
        return $this->storesToIndex;
    }
}
