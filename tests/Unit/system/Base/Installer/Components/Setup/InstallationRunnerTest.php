<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package   Tests\Unit\System\Base\Installer\Components\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Components\Setup;

use Codeception\Test\Unit;
use System\Base\Installer\Components\Setup\InstallationRunner;
use System\Base\Installer\Components\Setup\PasswordChecker;

/**
 * Unit test suite for InstallationRunner.
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 */
class InstallationRunnerTest extends Unit
{
    /**
     * Tests InstallationRunner initialization and runInstallation execution flow with weak password.
     *
     * @return void
     */
    public function testRunInstallationWeakPassword(): void
    {
        $mockView = new class {
            public int $responseCode = 0;
            public string $responseMessage = '';
            public function getParamsToView(): array
            {
                return [
                    'responseCode'    => $this->responseCode,
                    'responseMessage' => $this->responseMessage,
                ];
            }
        };

        $mockResponse = new class {
            public mixed $content = null;
            public function isSent(): bool { return false; }
            public function setJsonContent(mixed $c): void { $this->content = $c; }
            public function send(): string { return json_encode($this->content); }
        };

        $mockProgress = new class {
            public function resetProgress(): void {}
            public function preCheckComplete(bool $status = true): void {}
            public function unregisterMethods(array $m): void {}
        };

        $runner = new InstallationRunner(
            [],
            $mockView,
            $mockResponse,
            $mockProgress,
            null,
            null,
            new class extends PasswordChecker {
                public function checkPwStrength(string $pass): int|false { return 1; }
            }
        );

        $res = $runner->runInstallation(['dev' => 'false', 'pass' => '123456']);

        $this->assertSame(1, $mockView->responseCode);
        $this->assertStringContainsString('weak', strtolower($mockView->responseMessage));
    }
}
