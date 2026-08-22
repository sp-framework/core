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
use System\Base\Installer\Packages\Setup\PasswordChecker;

/**
 * Unit test suite for PasswordChecker.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class PasswordCheckerTest extends Unit
{
    /**
     * Tests password strength scores.
     *
     * @return void
     */
    public function testCheckPwStrength(): void
    {
        $checker = new PasswordChecker();

        $score = $checker->checkPwStrength('SimplePass123!');
        $this->assertIsInt($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(4, $score);
    }
}
