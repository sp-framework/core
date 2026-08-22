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
use System\Base\Installer\Components\Setup\PasswordChecker;

/**
 * Unit test suite for Components PasswordChecker.
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 */
class PasswordCheckerTest extends Unit
{
    /**
     * Tests password strength evaluation.
     *
     * @return void
     */
    public function testCheckPwStrength(): void
    {
        $checker = new PasswordChecker();
        $score = $checker->checkPwStrength('StrongPassword123!@#');

        $this->assertIsInt($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(4, $score);
    }
}
