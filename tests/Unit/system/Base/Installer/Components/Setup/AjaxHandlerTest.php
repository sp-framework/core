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
use System\Base\Installer\Components\Setup\AjaxHandler;
use System\Base\Installer\Components\Setup\PasswordChecker;

/**
 * Unit test suite for AjaxHandler.
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 */
class AjaxHandlerTest extends Unit
{
    /**
     * Tests AjaxHandler endpoints for password generate, password strength, and progress.
     *
     * @return void
     */
    public function testAjaxHandlerActions(): void
    {
        $mockView = new class {
            public int $responseCode = 0;
            public string $responseMessage = '';
            public mixed $responseData = null;
            public function getParamsToView(): array
            {
                return [
                    'responseCode'    => $this->responseCode,
                    'responseMessage' => $this->responseMessage,
                    'responseData'    => $this->responseData,
                ];
            }
        };

        $mockResponse = new class {
            public mixed $content = null;
            public function setContentType(string $t, string $c): void {}
            public function setHeader(string $n, string $v): void {}
            public function setJsonContent(mixed $c): void { $this->content = $c; }
            public function isSent(): bool { return false; }
            public function send(): string { return (string) json_encode($this->content); }
        };

        $mockRandom = new class {
            public function base62(int $len): string { return 'RandomKey12345'; }
        };

        $mockProgress = new class {
            public function getProgress(string $s, bool $r): array { return ['progress' => 80]; }
        };

        $handler = new AjaxHandler($mockView, $mockResponse, $mockRandom, $mockProgress, new PasswordChecker());

        // Password generation
        $handler->handle(['generatePw' => '1']);
        $this->assertSame('RandomKey12345', $mockView->responseData);

        // Password strength
        $handler->handle(['checkPwStrength' => '1', 'pass' => 'StrongPass123!@#']);
        $this->assertGreaterThanOrEqual(1, $mockView->responseData);

        // Progress
        $handler->handle(['session' => 'test-session-123']);
        $this->assertSame(['progress' => 80], $mockView->responseData);
    }
}
