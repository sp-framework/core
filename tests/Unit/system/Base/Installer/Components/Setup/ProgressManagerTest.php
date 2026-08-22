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
use System\Base\Installer\Components\Setup\ProgressManager;

/**
 * Unit test suite for ProgressManager.
 *
 * @package Tests\Unit\System\Base\Installer\Components\Setup
 */
class ProgressManagerTest extends Unit
{
    /**
     * Tests registerProgressMethods.
     *
     * @return void
     */
    public function testRegisterProgressMethods(): void
    {
        $registered = [];

        $mockProgress = new class ($registered) {
            public $reg;
            public function __construct(&$r) { $this->reg = &$r; }
            public function registerMethods(array $methods): void
            {
                $this->reg = $methods;
            }
        };

        $manager = new ProgressManager($mockProgress);
        $manager->registerProgressMethods();

        $this->assertNotEmpty($registered);
        $this->assertGreaterThan(15, count($registered));
    }
}
