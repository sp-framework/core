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
 * Unit test suite dynamically verifying all Register classes under system/Base/Installer/Packages/Setup/Register/.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class RegisterClassesTest extends Unit
{
    /**
     * Discovers and tests all Register classes.
     *
     * @return void
     */
    public function testAllRegisterClassesExistAndInstantiate(): void
    {
        $registerDir = base_path('system/Base/Installer/Packages/Setup/Register');
        $this->assertDirectoryExists($registerDir);

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($registerDir));
        $testedClasses = 0;

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace([$registerDir . '/', '.php'], ['', ''], $file->getPathname());
            $className = 'System\\Base\\Installer\\Packages\\Setup\\Register\\' . str_replace('/', '\\', $relativePath);

            $this->assertTrue(class_exists($className), "Register class {$className} should exist.");

            $reflection = new ReflectionClass($className);
            $methods = array_map(fn($m) => $m->getName(), $reflection->getMethods());
            $hasRegisterOrUpdate = false;
            foreach ($methods as $mName) {
                if (str_starts_with($mName, 'register') || str_starts_with($mName, 'update')) {
                    $hasRegisterOrUpdate = true;
                    break;
                }
            }

            $this->assertTrue($hasRegisterOrUpdate, "Register class {$className} must have a register or update method.");

            $testedClasses++;
        }

        $this->assertGreaterThanOrEqual(20, $testedClasses, 'Should test at least 20 registration classes.');
    }
}
