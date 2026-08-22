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
use System\Base\Installer\Packages\Setup\Schema;

/**
 * Unit test suite for Schema registry definitions.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class SchemaTest extends Unit
{
    /**
     * Tests that getSchema returns valid schema and model instances for standard mode.
     *
     * @return void
     */
    public function testGetSchemaReturnsAllStandardTables(): void
    {
        $schemaRegistry = new Schema();
        $schemaMap = $schemaRegistry->getSchema(false);

        $this->assertIsArray($schemaMap);
        $this->assertGreaterThan(50, count($schemaMap));

        $essentialTables = [
            'service_provider_core',
            'service_provider_apps',
            'service_provider_domains',
            'modules_components',
            'modules_packages',
            'modules_middlewares',
            'modules_views',
            'basepackages_users_accounts',
            'basepackages_users_roles',
            'basepackages_users_profiles',
            'basepackages_storages',
            'basepackages_workers_workers',
            'basepackages_workers_tasks',
            'basepackages_workers_schedules',
            'service_provider_api',
        ];

        foreach ($essentialTables as $tableName) {
            $this->assertArrayHasKey($tableName, $schemaMap);
            $this->assertArrayHasKey('schema', $schemaMap[$tableName]);
            $this->assertIsObject($schemaMap[$tableName]['schema']);

            if (method_exists($schemaMap[$tableName]['schema'], 'columns')) {
                $columnsDef = $schemaMap[$tableName]['schema']->columns();
                $this->assertIsArray($columnsDef);
                $this->assertArrayHasKey('columns', $columnsDef);
            }
        }
    }

    /**
     * Tests that dev mode flag adds devtools schema definitions if classes exist.
     *
     * @return void
     */
    public function testGetSchemaInDevMode(): void
    {
        $schemaRegistry = new Schema();
        $schemaMap = $schemaRegistry->getSchema(true);

        $this->assertIsArray($schemaMap);
        $this->assertGreaterThanOrEqual(50, count($schemaMap));
    }
}
