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
use System\Base\Installer\Packages\Setup\Cleaner;

/**
 * Unit test suite for Cleaner helper class.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class CleanerTest extends Unit
{
    /**
     * Tests cleanVar, cleanOldFfs, cleanOldAPIKeys, cleanOldBackups, cleanOldCookies.
     *
     * @return void
     */
    public function testCleanerOperations(): void
    {
        $deletedFiles = [];
        $deletedDirs = [];

        $mockLocalContent = new class ($deletedFiles, $deletedDirs) {
            public $deleted;
            public $deletedD;
            public function __construct(&$del, &$delD) { $this->deleted = &$del; $this->deletedD = &$delD; }
            public function delete(string $path): void { $this->deleted[] = $path; }
            public function deleteDirectory(string $path): void { $this->deletedD[] = $path; }
        };

        $mockUtils = new class {
            public function init(mixed $c): object
            {
                return new class {
                    public function scanDir(string $dir, bool $rec = false): array
                    {
                        return [
                            'files' => [$dir . 'file1.tmp', $dir . 'file2.log'],
                            'dirs'  => [$dir . 'sub/'],
                        ];
                    }
                };
            }
        };

        $mockBasepackages = (object) ['utils' => $mockUtils];

        $mockOpCache = new class {
            public bool $removed = false;
            public function removeCache(?string $f, string $type): void { $this->removed = true; }
        };

        $mockCookies = new class {
            public array $cookies = [];
            public function set(string $k, string $v, int $e, string $p, bool $s, string $h, bool $ht): void { $this->cookies[$k] = $v; }
            public function get(string $k): ?object { return new class { public function setOptions(array $o): void {} }; }
            public function send(): void {}
        };

        $mockRequest = new class {
            public function getHttpHost(): string { return 'localhost'; }
        };

        $cleaner = new Cleaner([], $mockLocalContent, $mockBasepackages, $mockOpCache, $mockCookies, $mockRequest);

        $this->assertTrue($cleaner->cleanVar());
        $this->assertTrue($mockOpCache->removed);

        $this->assertTrue($cleaner->cleanOldFfs());
        $this->assertTrue($cleaner->cleanOldAPIKeys());
        $this->assertTrue($cleaner->cleanOldBackups());
        $this->assertTrue($cleaner->cleanOldCookies());
        $this->assertArrayHasKey('SP', $mockCookies->cookies);
    }
}
