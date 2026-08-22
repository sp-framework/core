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
use System\Base\Installer\Packages\Setup\ModuleRegistrar;

/**
 * Unit test suite for ModuleRegistrar.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class ModuleRegistrarTest extends Unit
{
    /**
     * Tests ModuleRegistrar initialization and module registration proxies.
     *
     * @return void
     */
    public function testModuleRegistrarMethods(): void
    {
        $mockDb = new class {
            public function query(): object
            {
                return new class {
                    public function numRows(): int { return 0; }
                };
            }
        };

        $mockFf = new class {
            public function store(): object
            {
                return new class {
                    public function getById(): mixed { return null; }
                    public function insert(): mixed { return 1; }
                };
            }
        };

        $mockHelper = new class {
            public function decode(string $v, bool $a = true): mixed { return json_decode($v, $a); }
            public function encode(mixed $v): string { return json_encode($v); }
        };

        $mockLocalContent = new class {
            public function read(string $p): string { return '{}'; }
        };

        $mockBasepackages = new class {
            public $utils;
            public function __construct()
            {
                $this->utils = new class {
                    public function init(mixed $c): object
                    {
                        return new class {
                            public function scanDir(string $d, bool $r = false): array
                            {
                                return ['files' => []];
                            }
                        };
                    }
                };
            }
        };

        $registrar = new ModuleRegistrar(
            [],
            $mockDb,
            $mockFf,
            ['dev' => 'true'],
            $mockLocalContent,
            $mockBasepackages,
            $mockHelper
        );

        $this->assertFalse($registrar->registerModule('components'));
        $this->assertFalse($registrar->registerModule('packages'));
    }
}
