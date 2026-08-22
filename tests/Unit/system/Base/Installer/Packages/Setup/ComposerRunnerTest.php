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
use System\Base\Installer\Packages\Setup\ComposerRunner;

/**
 * Unit test suite for ComposerRunner.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class ComposerRunnerTest extends Unit
{
    /**
     * Tests ComposerRunner instantiation.
     *
     * @return void
     */
    public function testComposerRunnerInstantiation(): void
    {
        $runner = new ComposerRunner();
        $this->assertInstanceOf(ComposerRunner::class, $runner);
    }
}
