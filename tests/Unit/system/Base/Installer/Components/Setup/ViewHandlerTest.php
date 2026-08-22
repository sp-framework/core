<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Components\Setup;

use Codeception\Test\Unit;
use System\Base\Installer\Components\Setup\ViewHandler;

/**
 * Unit test suite for ViewHandler.
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 */
class ViewHandlerTest extends Unit
{
    /**
     * Tests ViewHandler renderView execution.
     *
     * @return void
     */
    public function testRenderViewOutputsView(): void
    {
        $mockView = new class {
            public mixed $countries = [];
            public mixed $timezones = [];
            public mixed $coreJson = [];
            public function render(string $tpl, array $params = []): string
            {
                return '<div>Rendered View</div>';
            }
        };

        $mockContainer = new class ($mockView) {
            public $view;
            public function __construct($v) { $this->view = $v; }
            public function getShared(string $name): mixed { return ($name === 'view') ? $this->view : null; }
        };

        $mockLocalContent = new class {
            public function fileExists(string $p): bool { return false; }
            public function read(string $p): string { return '[]'; }
        };

        $mockHelper = new class {
            public function decode(string $v, bool $a = true): mixed { return json_decode($v, $a); }
        };

        $mockRequest = new class {
            public function isPost(): bool { return false; }
            public function getHttpHost(): string { return 'localhost'; }
        };

        $mockResponse = new class {
            public function isSent(): bool { return false; }
        };

        $mockSession = new class {
            public function getId(): string { return 'sess-123'; }
        };

        $mockCookies = new class {
            public function useEncryption(bool $u): void {}
            public function set(string $k, string $v, int $e, string $p, bool $s, string $h, bool $ht, array $o = []): void {}
            public function send(): void {}
        };

        $mockSecurity = new class {};

        $handler = new ViewHandler(
            $mockContainer,
            $mockView,
            $mockLocalContent,
            $mockHelper,
            $mockRequest,
            $mockResponse,
            $mockSession,
            $mockCookies,
            $mockSecurity
        );

        ob_start();
        $handler->renderView([], false);
        $output = ob_get_clean();

        $this->assertStringContainsString('Rendered View', $output);
    }
}
