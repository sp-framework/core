<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Components
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Components;

use Codeception\Test\Unit;
use Phalcon\Config\Config as PhalconConfig;
use System\Base\Installer\Components\Setup;

/**
 * Unit test suite for Setup web controller Component.
 *
 * @package Tests\Unit\System\Base\Installer\Components
 */
class SetupTest extends Unit
{
    /**
     * Creates a mock DI container for component testing.
     *
     * @param array<string, mixed> $postData
     *
     * @return object
     */
    protected function createMockDiContainer(array $postData = []): object
    {
        $mockView = new class {
            public int $responseCode = 0;
            public string $responseMessage = '';
            public mixed $responseData = null;
            public mixed $countries = [];
            public mixed $timezones = [];
            public mixed $coreJson = [];
            public function setViewsDir(string $dir): void {}
            public function getParamsToView(): array
            {
                return [
                    'responseCode'    => $this->responseCode,
                    'responseMessage' => $this->responseMessage,
                    'responseData'    => $this->responseData,
                ];
            }
            public function render(string $view, array $params = []): string
            {
                return '<html>Mock Setup HTML</html>';
            }
        };

        $mockResponse = new class {
            public mixed $jsonContent = null;
            public bool $isSent = false;
            public function setContentType(string $type, string $charset): void {}
            public function setHeader(string $name, string $val): void {}
            public function setJsonContent(mixed $content): void { $this->jsonContent = $content; }
            public function isSent(): bool { return $this->isSent; }
            public function send(): string { $this->isSent = true; return (string) json_encode($this->jsonContent); }
        };

        $mockRequest = new class ($postData) {
            public array $post;
            public function __construct(array $post) { $this->post = $post; }
            public function isPost(): bool { return count($this->post) > 0; }
            public function isGet(): bool { return count($this->post) === 0; }
            public function getPost(): array { return $this->post; }
            public function getHttpHost(): string { return 'localhost'; }
            public function getServer(string $k): string { return '127.0.0.1'; }
        };

        $mockSession = new class {
            public function getId(): string { return 'test-session-123'; }
            public function get(string $key): mixed { return null; }
            public function set(string $key, mixed $val): void {}
            public function has(string $key): bool { return false; }
            public function destroy(): void {}
        };

        $mockCookies = new class {
            public function set(string $name, string $val, int $expire = 0, string $path = '/', bool $secure = false, string $domain = '', bool $httpOnly = false, array $options = []): void {}
            public function get(string $name): ?object { return new class { public function setOptions(array $opts): void {} }; }
            public function send(): void {}
            public function useEncryption(bool $use): void {}
        };

        $mockSecurity = new class {
            public function hash(string $pass, array $opts = []): string { return password_hash($pass, PASSWORD_BCRYPT, $opts); }
        };

        $mockRandom = new class {
            public function base62(int $len): string { return 'RandomPass1234'; }
            public function base58(int $len): string { return 'RandomKey58'; }
        };

        $mockHelper = new class {
            public function encode(mixed $val): string { return (string) json_encode($val); }
            public function decode(string $val, bool $assoc = true): mixed { return json_decode($val, $assoc); }
        };

        $mockValidation = new class {
            public function add(string $field, string $validator, array $options = []): void {}
            public function validate(array $data): array { return []; }
        };

        $mockProgress = new class {
            public function init(mixed $c, string $type): object { return $this; }
            public function updateProgress(string $step, mixed $status = null, bool $broadcast = false, ?string $sub = null): void {}
            public function checkProgressFile(): bool { return false; }
            public function deleteProgressFile(): void {}
            public function registerMethods(array $methods): void {}
            public function preCheckComplete(bool $ok = true): void {}
            public function resetProgress(): void {}
            public function getProgress(string $session, bool $reset = false): array { return ['progress' => 50]; }
            public function getCallResult(string $method): ?bool { return true; }
        };

        $mockBasepackages = new class ($mockProgress) {
            public $progress;
            public $utils;
            public function __construct($prog)
            {
                $this->progress = $prog;
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

        $mockLocalContent = new class {
            public function read(string $path): string { return '[]'; }
            public function fileExists(string $path): bool { return false; }
            public function write(string $path, string $content, array $config = []): void {}
        };

        $services = [
            'view'             => $mockView,
            'response'         => $mockResponse,
            'request'          => $mockRequest,
            'session'          => $mockSession,
            'cookies'          => $mockCookies,
            'security'         => $mockSecurity,
            'random'           => $mockRandom,
            'helper'           => $mockHelper,
            'validation'       => $mockValidation,
            'basepackages'     => $mockBasepackages,
            'localContent'     => $mockLocalContent,
            'remoteWebContent' => null,
            'opCache'          => null,
        ];

        return new class ($services) {
            public $services;
            public function __construct($s) { $this->services = $s; }
            public function getShared(string $name): mixed { return $this->services[$name] ?? null; }
            public function get(string $name): mixed { return $this->services[$name] ?? null; }
            public function has(string $name): bool { return isset($this->services[$name]); }
        };
    }

    /**
     * Tests Setup component instantiation.
     *
     * @return void
     */
    public function testSetupComponentInstantiation(): void
    {
        $mockDi = $this->createMockDiContainer();
        $configs = ['setup' => true, 'databasetype' => 'ff'];

        $setupComponent = new Setup($mockDi->getShared('session'), $configs, false, $mockDi);
        $this->assertInstanceOf(Setup::class, $setupComponent);
    }

    /**
     * Tests Setup run method in GET mode outputs rendered template.
     *
     * @return void
     */
    public function testSetupRunGetRendersView(): void
    {
        $mockDi = $this->createMockDiContainer();
        $configs = ['setup' => true, 'databasetype' => 'ff'];

        $setupComponent = new Setup($mockDi->getShared('session'), $configs, false, $mockDi);

        ob_start();
        $setupComponent->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Mock Setup HTML', $output);
    }

    /**
     * Tests Setup AJAX password generator endpoint.
     *
     * @return void
     */
    public function testSetupAjaxGeneratePassword(): void
    {
        $postData = [
            'session'    => 'test-session-123',
            'generatePw' => '1',
        ];

        $mockDi = $this->createMockDiContainer($postData);
        $configs = ['setup' => true, 'databasetype' => 'ff'];

        $setupComponent = new Setup($mockDi->getShared('session'), $configs, false, $mockDi);
        $setupComponent->run();

        $view = $mockDi->getShared('view');
        $this->assertSame(0, $view->responseCode);
        $this->assertSame('RandomPass1234', $view->responseData);
    }

    /**
     * Tests Setup AJAX password strength endpoint.
     *
     * @return void
     */
    public function testSetupAjaxCheckPasswordStrength(): void
    {
        $postData = [
            'session'         => 'test-session-123',
            'checkPwStrength' => '1',
            'pass'            => 'SuperSecurePass123!@#',
        ];

        $mockDi = $this->createMockDiContainer($postData);
        $configs = ['setup' => true, 'databasetype' => 'ff'];

        $setupComponent = new Setup($mockDi->getShared('session'), $configs, false, $mockDi);
        $setupComponent->run();

        $view = $mockDi->getShared('view');
        $this->assertSame(0, $view->responseCode);
        $this->assertGreaterThanOrEqual(1, $view->responseData);
    }

    /**
     * Tests Setup AJAX progress tracking polling endpoint.
     *
     * @return void
     */
    public function testSetupAjaxPollProgress(): void
    {
        $postData = [
            'session' => 'test-session-123',
        ];

        $mockDi = $this->createMockDiContainer($postData);
        $configs = ['setup' => true, 'databasetype' => 'ff'];

        $setupComponent = new Setup($mockDi->getShared('session'), $configs, false, $mockDi);
        $setupComponent->run();

        $view = $mockDi->getShared('view');
        $this->assertSame(0, $view->responseCode);
        $this->assertSame(['progress' => 50], $view->responseData);
    }
}
