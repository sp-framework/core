<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package   Tests\Unit\System\Base\Installer\Packages\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages\Setup;

use Codeception\Test\Unit;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * Unit test suite dynamically verifying all 77 schema classes under system/Base/Installer/Packages/Setup/Schema/.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class SchemaClassesTest extends Unit
{
    /**
     * Discovers and tests all Schema classes.
     *
     * @return void
     */
    public function testAllSchemaClassesExistAndReturnValidStructure(): void
    {
        $schemaDir = base_path('system/Base/Installer/Packages/Setup/Schema');
        $this->assertDirectoryExists($schemaDir);

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($schemaDir));
        $testedClasses = 0;

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace([$schemaDir . '/', '.php'], ['', ''], $file->getPathname());
            $className = 'System\\Base\\Installer\\Packages\\Setup\\Schema\\' . str_replace('/', '\\', $relativePath);

            $this->assertTrue(class_exists($className), "Schema class {$className} should exist.");

            $reflection = new ReflectionClass($className);
            $this->assertTrue($reflection->hasMethod('columns'), "Schema class {$className} must have columns() method.");

            $instance = new $className();
            $columns = $instance->columns();

            $this->assertIsArray($columns, "{$className}::columns() must return an array.");
            $this->assertArrayHasKey('columns', $columns, "{$className}::columns() must contain key 'columns'.");
            $this->assertNotEmpty($columns['columns'], "{$className}::columns()['columns'] must not be empty.");

            if ($reflection->hasMethod('indexes')) {
                $indexes = $instance->indexes();
                $this->assertIsArray($indexes, "{$className}::indexes() must return an array.");
            }

            $testedClasses++;
        }

        $this->assertGreaterThanOrEqual(75, $testedClasses, 'Should test at least 75 schema definition classes.');
    }
}
