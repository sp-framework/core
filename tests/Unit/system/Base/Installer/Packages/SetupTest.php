<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Packages
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages;

use Codeception\Test\Unit;
use System\Base\Installer\Packages\Setup;

/**
 * Unit test suite for Setup package orchestrator.
 *
 * @package Tests\Unit\System\Base\Installer\Packages
 */
class SetupTest extends Unit
{
    /**
     * Creates mock DI container.
     */
    protected function createMockContainer(): array
    {
        $mockRequest = new class {
            public function isPost(): bool { return true; }
            public function isGet(): bool { return false; }
            public function getHttpHost(): string { return 'localhost'; }
            public function getServer(string $k): string { return '127.0.0.1'; }
        };

        $mockSession = new class {
            public function getId(): string { return 'test-session-123'; }
        };

        $mockValidation = new class {
            public function add(string $field, string $validator, array $options = []): void {}
            public function validate(array $data): array { return []; }
        };

        $mockSecurity = new class {
            public function hash(string $pass, array $options = []): string
            {
                return password_hash($pass, PASSWORD_BCRYPT, $options);
            }
        };

        $mockCookies = new class {
            public function set(string $name, string $val, int $expire = 0, string $path = '/', bool $secure = false, string $domain = '', bool $httpOnly = false, array $options = []): void {}
            public function get(string $name): ?object
            {
                return new class { public function setOptions(array $opts): void {} };
            }
            public function send(): void {}
            public function useEncryption(bool $use): void {}
        };

        $mockHelper = new class {
            public function encode(mixed $val): string { return (string) json_encode($val); }
            public function decode(string $val, bool $assoc = true): mixed { return json_decode($val, $assoc); }
        };

        $mockBasepackages = new class {
            public $progress;
            public $utils;
            public function __construct()
            {
                $this->progress = new class {
                    public function updateProgress(string $step, mixed $status = null, bool $broadcast = false, ?string $sub = null): void {}
                    public function preCheckComplete(bool $ok = true): void {}
                    public function resetProgress(): void {}
                };
                $this->utils = new class {
                    public function init(mixed $c): object
                    {
                        return new class {
                            public function scanDir(string $dir, bool $rec = false): array
                            {
                                return ['files' => [], 'dirs' => []];
                            }
                        };
                    }
                };
            }
        };

        return [
            'request'      => $mockRequest,
            'session'      => $mockSession,
            'validation'   => $mockValidation,
            'security'     => $mockSecurity,
            'cookies'      => $mockCookies,
            'helper'       => $mockHelper,
            'opCache'      => null,
            'basepackages' => $mockBasepackages,
            'localContent' => null,
            'remoteWebContent' => null,
        ];
    }

    /**
     * Tests Setup package initialization and validateData method with valid payload.
     *
     * @return void
     */
    public function testSetupValidateDataSuccess(): void
    {
        $container = $this->createMockContainer();
        $postData = [
            'databasetype' => 'ff',
            'email'        => 'admin@test.com',
            'pass'         => 'SecretPass123!',
        ];

        $setup = new Setup($container, $postData, false, false);
        $result = $setup->validateData();

        $this->assertTrue($result);
    }

    /**
     * Tests Setup __call magic method proxies internal methods and tracks progress.
     *
     * @return void
     */
    public function testSetupCallProxy(): void
    {
        $container = $this->createMockContainer();
        $postData = ['databasetype' => 'ff'];

        $setup = new Setup($container, $postData, false, false);

        // Calling cleanOldCookies via __call proxy
        $result = $setup->cleanOldCookies();
        $this->assertTrue($result);
    }

    /**
     * Tests checkPwStrength score evaluator.
     *
     * @return void
     */
    public function testSetupCheckPwStrength(): void
    {
        $container = $this->createMockContainer();
        $setup = new Setup($container, ['databasetype' => 'ff']);

        $score = $setup->checkPwStrength('Simple123!');
        $this->assertIsInt($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(4, $score);
    }
}
