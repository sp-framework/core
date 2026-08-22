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
use System\Base\Installer\Packages\Setup\SeedRegistrar;

/**
 * Unit test suite for SeedRegistrar.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class SeedRegistrarTest extends Unit
{
    /**
     * Tests SeedRegistrar method delegation for repos, domain, and workers.
     *
     * @return void
     */
    public function testSeedRegistrarDelegation(): void
    {
        $mockDb = new class {
            public function query(): object
            {
                return new class {
                    public function numRows(): int { return 0; }
                };
            }
            public function insertAsDict(string $table, array $data): void {}
            public function lastInsertId(): int { return 1; }
        };

        $mockFf = new class {
            public function store(): object
            {
                return new class {
                    public function getById(): mixed { return null; }
                    public function insert(): mixed { return 1; }
                    public function insertAsDict(): mixed { return 1; }
                    public function updateOrInsert(array $d): mixed { return 1; }
                    public function getLastInsertedId(): mixed { return 1; }
                    public function findBy(): mixed { return null; }
                };
            }
        };

        $mockHelper = new class {
            public function decode(string $v, bool $a = true): mixed { return json_decode($v, $a); }
            public function encode(mixed $v): string { return json_encode($v); }
        };

        $mockRequest = new class {
            public function getHttpHost(): string { return 'localhost'; }
            public function setStrictHostCheck(bool $b): void {}
        };

        $mockLocalContent = new class {
            public function read(string $p): string { return '{}'; }
        };

        $registrar = new SeedRegistrar(
            $mockDb,
            $mockFf,
            ['pass' => 'admin', 'email' => 'admin@example.com'],
            $mockRequest,
            $mockHelper,
            $mockLocalContent
        );

        $this->assertTrue($registrar->registerRepos());
        $this->assertTrue($registrar->registerDomain());
        $this->assertTrue($registrar->registerWorkers());
        $this->assertTrue($registrar->registerSchedules());
        $this->assertTrue($registrar->registerTasks());
    }
}
