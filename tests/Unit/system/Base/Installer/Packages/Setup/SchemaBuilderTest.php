<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages\Setup;

use Codeception\Test\Unit;
use System\Base\Installer\Packages\Setup\SchemaBuilder;

/**
 * Unit test suite for SchemaBuilder.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class SchemaBuilderTest extends Unit
{
    /**
     * Tests buildSchema and performIndexing in hybrid/ff mode.
     *
     * @return void
     */
    public function testSchemaBuilderOperations(): void
    {
        $createdTables = [];
        $storesGenerated = [];

        $mockDb = new class ($createdTables) {
            public $tables;
            public function __construct(&$t) { $this->tables = &$t; }
            public function createTable(string $table, string $db, array $cols): void
            {
                $this->tables[] = $table;
            }
        };

        $mockFf = new class ($storesGenerated) {
            public $stores;
            public function __construct(&$s) { $this->stores = &$s; }
            public function generateConfig(string $t, object $s, ?object $m, array $p): array { return []; }
            public function generateSchema(string $t, object $s, ?object $m): array { return []; }
            public function store(string $t, array $c = [], array $s = [], mixed $f = null): object
            {
                $this->stores[] = $t;
                return new class {
                    public bool $reindexed = false;
                    public function deleteStore(): void {}
                    public function reIndexStore(): void { $this->reindexed = true; }
                };
            }
        };

        $postData = [
            'databasetype' => 'hybrid',
            'dbname'       => 'test_db',
            'dev'          => false,
        ];

        $builder = new SchemaBuilder($mockDb, $mockFf, $postData);

        $this->assertTrue($builder->buildSchema());
        $this->assertNotEmpty($createdTables);
        $this->assertNotEmpty($storesGenerated);
        $this->assertTrue($builder->performIndexing());
    }
}
