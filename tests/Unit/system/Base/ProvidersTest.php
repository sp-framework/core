<?php

/**
 * SP Framework
 *
 * @package   Tests\System\Base
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\System\Base;

use Codeception\Test\Unit;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AccessServiceProvider;
use System\Base\Providers\ConfigServiceProvider;
use System\Base\Providers\ContentServiceProvider;
use System\Base\Providers\DatabaseServiceProvider;
use System\Base\Providers\DispatcherServiceProvider;
use System\Base\Providers\ErrorServiceProvider;
use System\Base\Providers\RouterServiceProvider;
use System\Base\Providers\SecurityServiceProvider;
use System\Base\Providers\SessionServiceProvider;
use System\Base\Providers\TerminalServiceProvider;
use System\Base\Providers\ViewServiceProvider;
use Tests\Support\UnitTester;

/**
 * Class ProvidersTest
 *
 * Comprehensive unit test suite for system/Base/Providers.php service provider registration matrix.
 * Evaluates array structures, class autoloading, interface conformance, mode requirements, and duplicate detection.
 *
 * @package Tests\System\Base
 */
class ProvidersTest extends Unit
{
    /**
     * Unit tester actor instance.
     *
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * Loaded providers array from configuration file.
     *
     * @var array
     */
    protected array $providers;

    /**
     * Sets up test environment before each test execution.
     *
     * @return void
     */
    protected function _before(): void
    {
        $providersFile = dirname(__DIR__, 4) . '/system/Base/Providers.php';
        $this->providers = include $providersFile;
    }

    /**
     * Tests that Providers.php returns a valid array with required execution mode keys.
     *
     * @return void
     */
    public function testProvidersFileReturnsValidArrayStructure(): void
    {
        $this->assertIsArray($this->providers);
        $this->assertArrayHasKey('mvc', $this->providers);
        $this->assertArrayHasKey('cli', $this->providers);
        $this->assertArrayHasKey('api', $this->providers);
    }

    /**
     * Tests that all execution modes define a non-empty list of service providers.
     *
     * @return void
     */
    public function testAllModesContainNonEmptyProvidersList(): void
    {
        foreach (['mvc', 'cli', 'api'] as $mode) {
            $this->assertIsArray($this->providers[$mode]);
            $this->assertNotEmpty($this->providers[$mode], "Mode '{$mode}' provider list should not be empty.");
        }
    }

    /**
     * Tests that every service provider class configured in all modes exists and can be autoloaded.
     *
     * @return void
     */
    public function testAllProviderClassesExistAndAutoload(): void
    {
        foreach ($this->providers as $mode => $classList) {
            foreach ($classList as $providerClass) {
                $this->assertTrue(
                    class_exists($providerClass),
                    "Service provider class '{$providerClass}' in mode '{$mode}' must exist."
                );
            }
        }
    }

    /**
     * Tests that every service provider class implements Phalcon ServiceProviderInterface.
     *
     * @return void
     */
    public function testAllProvidersImplementServiceProviderInterface(): void
    {
        foreach ($this->providers as $mode => $classList) {
            foreach ($classList as $providerClass) {
                $reflection = new \ReflectionClass($providerClass);
                $this->assertTrue(
                    $reflection->implementsInterface(ServiceProviderInterface::class),
                    "Service provider '{$providerClass}' in mode '{$mode}' must implement ServiceProviderInterface."
                );
            }
        }
    }

    /**
     * Tests that MVC mode contains all essential full-stack web application providers.
     *
     * @return void
     */
    public function testMvcModeContainsEssentialProviders(): void
    {
        $mvcProviders = $this->providers['mvc'];

        $this->assertContains(ConfigServiceProvider::class, $mvcProviders);
        $this->assertContains(DatabaseServiceProvider::class, $mvcProviders);
        $this->assertContains(RouterServiceProvider::class, $mvcProviders);
        $this->assertContains(DispatcherServiceProvider::class, $mvcProviders);
        $this->assertContains(ViewServiceProvider::class, $mvcProviders);
        $this->assertContains(SecurityServiceProvider::class, $mvcProviders);
        $this->assertContains(AccessServiceProvider::class, $mvcProviders);
        $this->assertContains(ErrorServiceProvider::class, $mvcProviders);
    }

    /**
     * Tests that CLI mode contains all essential command-line and console providers.
     *
     * @return void
     */
    public function testCliModeContainsEssentialProviders(): void
    {
        $cliProviders = $this->providers['cli'];

        $this->assertContains(ConfigServiceProvider::class, $cliProviders);
        $this->assertContains(DatabaseServiceProvider::class, $cliProviders);
        $this->assertContains(TerminalServiceProvider::class, $cliProviders);
        $this->assertContains(SessionServiceProvider::class, $cliProviders);
        $this->assertContains(SecurityServiceProvider::class, $cliProviders);
    }

    /**
     * Tests that API mode contains all essential RESTful micro-service providers.
     *
     * @return void
     */
    public function testApiModeContainsEssentialProviders(): void
    {
        $apiProviders = $this->providers['api'];

        $this->assertContains(ConfigServiceProvider::class, $apiProviders);
        $this->assertContains(DatabaseServiceProvider::class, $apiProviders);
        $this->assertContains(RouterServiceProvider::class, $apiProviders);
        $this->assertContains(ContentServiceProvider::class, $apiProviders);
        $this->assertContains(SecurityServiceProvider::class, $apiProviders);
        $this->assertContains(AccessServiceProvider::class, $apiProviders);
    }

    /**
     * Tests that there are no duplicate provider declarations within any execution mode.
     *
     * @return void
     */
    public function testNoDuplicateProvidersInAnyMode(): void
    {
        foreach ($this->providers as $mode => $classList) {
            $uniqueList = array_unique($classList);
            $this->assertSame(
                count($uniqueList),
                count($classList),
                "Duplicate service provider detected in mode '{$mode}'."
            );
        }
    }
}
