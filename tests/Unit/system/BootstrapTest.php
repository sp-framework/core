<?php

/**
 * SP Framework
 *
 * @package   Tests\System
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\System;

use Codeception\Test\Unit;
use Phalcon\Di\DiInterface;
use System\Bootstrap;
use Tests\Support\UnitTester;

/**
 * Interface TestDiContainerInterface
 *
 * Extended DI interface adding register() method signature for testing provider registration.
 *
 * @package Tests\System
 */
interface TestDiContainerInterface extends DiInterface
{
    /**
     * Registers a service provider instance.
     *
     * @param object $provider The service provider to register.
     *
     * @return void
     */
    public function register($provider): void;
}

/**
 * Class TestableBootstrap
 *
 * Testable subclass of Bootstrap allowing interception of environment checks, container creation,
 * CLI execution, and process exit calls.
 *
 * @package Tests\System
 */
class TestableBootstrap extends Bootstrap
{
    /**
     * Flag indicating whether terminate() was invoked.
     *
     * @var bool
     */
    public bool $terminated = false;

    /**
     * Process exit status code passed to terminate().
     *
     * @var int
     */
    public int $exitCode = 0;

    /**
     * Override flag to mock isCli() response. Null uses system SAPI.
     *
     * @var bool|null
     */
    public ?bool $mockCli = null;

    /**
     * Error output captured from writeCliError().
     *
     * @var string
     */
    public string $cliErrorOutput = '';

    /**
     * Flag indicating whether handleApiRequest() was executed.
     *
     * @var bool
     */
    public bool $apiHandled = false;

    /**
     * Flag indicating whether handleMvcRequest() was executed.
     *
     * @var bool
     */
    public bool $mvcHandled = false;

    /**
     * Override flag for determineExecutionMode(). Null uses standard detection.
     *
     * @var bool|null
     */
    public ?bool $mockIsApi = null;

    /**
     * Mock DI container instance for web requests.
     *
     * @var DiInterface|null
     */
    public ?DiInterface $mockContainer = null;

    /**
     * Mock DI container instance for CLI tasks.
     *
     * @var DiInterface|null
     */
    public ?DiInterface $mockCliContainer = null;

    /**
     * Mock Console instance for CLI task dispatching.
     *
     * @var object|null
     */
    public ?object $mockConsole = null;

    /**
     * Overrides autoloader initialization to avoid stubbed loader execution in CLI unit test environment.
     *
     * @return void
     */
    protected function initLoader(): void
    {
    }

    /**
     * Checks whether the current context is CLI, allowing test override.
     *
     * @return bool True if CLI mode, false otherwise.
     */
    protected function isCli(): bool
    {
        if ($this->mockCli !== null) {
            return $this->mockCli;
        }

        return parent::isCli();
    }

    /**
     * Intercepts process termination.
     *
     * @param int $code Exit status code.
     *
     * @return void
     */
    protected function terminate(int $code = 0): void
    {
        $this->terminated = true;
        $this->exitCode = $code;
    }

    /**
     * Captures CLI error messages in memory.
     *
     * @param string $message The formatted error message.
     *
     * @return void
     */
    protected function writeCliError(string $message): void
    {
        $this->cliErrorOutput .= $message;
    }

    /**
     * Intercepts API request handling.
     *
     * @param DiInterface $container Active DI container.
     *
     * @return void
     */
    protected function handleApiRequest(DiInterface $container): void
    {
        $this->apiHandled = true;
    }

    /**
     * Intercepts MVC request handling.
     *
     * @param DiInterface $container Active DI container.
     *
     * @return void
     */
    protected function handleMvcRequest(DiInterface $container): void
    {
        $this->mvcHandled = true;
    }

    /**
     * Overrides execution mode detection for testing.
     *
     * @param DiInterface $container Active DI container.
     *
     * @return bool True if API mode, false otherwise.
     */
    protected function determineExecutionMode(DiInterface $container): bool
    {
        if ($this->mockIsApi !== null) {
            $this->isApi = $this->mockIsApi;
            return $this->mockIsApi;
        }

        return parent::determineExecutionMode($container);
    }

    /**
     * Overrides container creation to supply mock DI container.
     *
     * @return DiInterface
     */
    protected function initContainer(): DiInterface
    {
        if ($this->mockContainer !== null) {
            return $this->mockContainer;
        }

        return parent::initContainer();
    }

    /**
     * Overrides CLI container creation to supply mock CLI DI container.
     *
     * @return DiInterface
     */
    protected function initCliContainer(): DiInterface
    {
        if ($this->mockCliContainer !== null) {
            return $this->mockCliContainer;
        }

        return parent::initCliContainer();
    }

    /**
     * Overrides Console application creation to supply mock console.
     *
     * @param DiInterface $container CLI DI container.
     *
     * @return object
     */
    protected function initConsole(DiInterface $container): object
    {
        if ($this->mockConsole !== null) {
            return $this->mockConsole;
        }

        return parent::initConsole($container);
    }

    /**
     * Exposes protected parseCliArguments method for unit testing.
     *
     * @param array $arguments Raw CLI arguments.
     *
     * @return array Parsed task/action parameters.
     */
    public function callParseCliArguments(array $arguments): array
    {
        return $this->parseCliArguments($arguments);
    }

    /**
     * Exposes protected configureEnvironment method for unit testing.
     *
     * @param DiInterface $container Target DI container.
     *
     * @return void
     */
    public function callConfigureEnvironment(DiInterface $container): void
    {
        $this->configureEnvironment($container);
    }

    /**
     * Exposes protected determineExecutionMode method for unit testing.
     *
     * @param DiInterface $container Active DI container.
     *
     * @return bool True if API mode, false otherwise.
     */
    public function callDetermineExecutionMode(DiInterface $container): bool
    {
        return $this->determineExecutionMode($container);
    }

    /**
     * Exposes protected registerProviders method for unit testing.
     *
     * @param DiInterface $container Target DI container.
     * @param array       $providers Service provider class list.
     *
     * @return void
     */
    public function callRegisterProviders(DiInterface $container, array $providers): void
    {
        $this->registerProviders($container, $providers);
    }
}

/**
 * Class BootstrapTest
 *
 * Unit test suite evaluating Bootstrap lifecycle, CLI argument parsing,
 * provider registration, API vs MVC routing, and error resilience.
 *
 * @package Tests\System
 */
class BootstrapTest extends Unit
{
    /**
     * Codeception unit tester actor.
     *
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * Verifies that the Bootstrap constructor merges and initializes custom service providers.
     *
     * @return void
     */
    public function testConstructorInitializesCustomProviders(): void
    {
        $customProviders = [
            'mvc' => ['Custom\\Provider\\One', 'Custom\\Provider\\Two'],
        ];
        $bootstrap = new TestableBootstrap(null, $customProviders, false);

        $providers = $bootstrap->getProviders();
        $this->assertContains('Custom\\Provider\\One', $providers['mvc']);
        $this->assertContains('Custom\\Provider\\Two', $providers['mvc']);
    }

    /**
     * Verifies setting and getting the service providers list on the Bootstrap instance.
     *
     * @return void
     */
    public function testSetAndGetProviders(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $providers = ['ProviderA', 'ProviderB'];

        $bootstrap->setProviders($providers);
        $this->assertSame($providers, $bootstrap->getProviders());
    }

    /**
     * Verifies CLI argument parsing across various token counts.
     *
     * @return void
     */
    public function testParseCliArgumentsWithVariousInputs(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();

        // No task provided -> empty array
        $parsed = $bootstrap->callParseCliArguments(['app/cli.php']);
        $this->assertSame([], $parsed);

        // Task only -> main action
        $parsed = $bootstrap->callParseCliArguments(['app/cli.php', 'cron']);
        $this->assertSame('cron', $parsed['task']);
        $this->assertArrayNotHasKey('action', $parsed);

        // Task and action
        $parsed = $bootstrap->callParseCliArguments(['app/cli.php', 'maintenance', 'cleanup']);
        $this->assertSame('maintenance', $parsed['task']);
        $this->assertSame('cleanup', $parsed['action']);

        // Task, action, and multiple parameters
        $parsed = $bootstrap->callParseCliArguments(['app/cli.php', 'user', 'create', 'admin', 'admin@example.com']);
        $this->assertSame('user', $parsed['task']);
        $this->assertSame('create', $parsed['action']);
        $this->assertSame(['admin', 'admin@example.com'], $parsed['params']);
    }

    /**
     * Verifies that mvc() terminates immediately with code 1 when invoked in CLI execution mode.
     *
     * @return void
     */
    public function testMvcTerminatesWhenCalledInCliMode(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = true;

        $bootstrap->mvc();

        $this->assertTrue($bootstrap->terminated);
        $this->assertSame(1, $bootstrap->exitCode);
    }

    /**
     * Verifies that cli() terminates immediately with code 1 when invoked in non-CLI mode.
     *
     * @return void
     */
    public function testCliTerminatesWhenCalledInNonCliMode(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = false;

        $bootstrap->cli(['app/cli.php', 'test']);

        $this->assertTrue($bootstrap->terminated);
        $this->assertSame(1, $bootstrap->exitCode);
    }

    /**
     * Verifies environment configuration sets timezone and debug error reporting.
     *
     * @return void
     */
    public function testConfigureEnvironmentSetsTimezoneAndDebugReporting(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $container = $this->createMock(DiInterface::class);

        $bootstrap->config = (object) [
            'locale' => (object) [
                'timezone' => 'Australia/Sydney',
            ],
            'debug' => true,
        ];

        $bootstrap->callConfigureEnvironment($container);

        $this->assertSame('Australia/Sydney', date_default_timezone_get());
        $this->assertSame(E_ALL, error_reporting());
    }

    /**
     * Verifies that determineExecutionMode correctly detects API provider.
     *
     * @return void
     */
    public function testDetermineExecutionModeChecksApiProvider(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();

        $apiMock = new class {
            public function isApi(): bool { return true; }
        };
        $container = $this->createMock(DiInterface::class);
        $container->method('getShared')->with('api')->willReturn($apiMock);

        $isApi = $bootstrap->callDetermineExecutionMode($container);
        $this->assertTrue($isApi);

        $containerNonApi = $this->createMock(DiInterface::class);
        $containerNonApi->method('getShared')->with('api')->willReturn(null);

        $isApi = $bootstrap->callDetermineExecutionMode($containerNonApi);
        $this->assertFalse($isApi);
    }

    /**
     * Verifies that registerProviders iterates through the provider list and registers each with the container.
     *
     * @return void
     */
    public function testRegisterProvidersIteratesAndRegisters(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();

        $registered = [];
        $container = $this->createMock(TestDiContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('register')
            ->willReturnCallback(function ($provider) use (&$registered) {
                $registered[] = get_class($provider);
            });

        $dummyProvider1 = new class {
            public function register(DiInterface $container): void {}
        };
        $dummyProvider2 = new class {
            public function register(DiInterface $container): void {}
        };

        $bootstrap->callRegisterProviders($container, [
            get_class($dummyProvider1),
            get_class($dummyProvider2),
        ]);

        $this->assertCount(2, $registered);
        $this->assertSame(get_class($dummyProvider1), $registered[0]);
        $this->assertSame(get_class($dummyProvider2), $registered[1]);
    }

    /**
     * Verifies that mvc() routes to handleApiRequest when API mode is active.
     *
     * @return void
     */
    public function testMvcRoutesToApiExecutionWhenApiDetected(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = false;
        $bootstrap->mockIsApi = true;

        $container = $this->createMock(TestDiContainerInterface::class);
        $bootstrap->mockContainer = $container;
        $bootstrap->setProviders([]);

        $bootstrap->mvc();

        $this->assertTrue($bootstrap->apiHandled);
        $this->assertFalse($bootstrap->mvcHandled);
    }

    /**
     * Verifies that mvc() routes to handleMvcRequest when standard web mode is active.
     *
     * @return void
     */
    public function testMvcRoutesToMvcExecutionWhenStandardWebDetected(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = false;
        $bootstrap->mockIsApi = false;

        $container = $this->createMock(TestDiContainerInterface::class);
        $bootstrap->mockContainer = $container;
        $bootstrap->setProviders([]);

        $bootstrap->mvc();

        $this->assertTrue($bootstrap->mvcHandled);
        $this->assertFalse($bootstrap->apiHandled);
    }

    /**
     * Verifies that cli() executes tasks successfully and commits the logger.
     *
     * @return void
     */
    public function testCliExecutesSuccessfullyAndCommitsLogger(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = true;
        $bootstrap->setProviders([]);

        $logger = new class {
            public bool $committed = false;
            public function commit(): void
            {
                $this->committed = true;
            }
        };

        $container = $this->createMock(TestDiContainerInterface::class);
        $container->method('getShared')->willReturnCallback(function ($name) use ($logger) {
            if ($name === 'logger') {
                return $logger;
            }
            return null;
        });
        $bootstrap->mockCliContainer = $container;

        $mockConsole = new class {
            public bool $handled = false;
            public array $arguments = [];
            public function handle(array $arguments): void
            {
                $this->handled = true;
                $this->arguments = $arguments;
            }
        };
        $bootstrap->mockConsole = $mockConsole;

        $bootstrap->cli(['app/cli.php', 'cron', 'run']);

        $this->assertTrue($mockConsole->handled);
        $this->assertSame('cron', $mockConsole->arguments['task']);
        $this->assertSame('run', $mockConsole->arguments['action']);
        $this->assertTrue($logger->committed);
        $this->assertFalse($bootstrap->terminated);
    }

    /**
     * Verifies that cli() catches execution exceptions, logs error messages, and terminates with code 1.
     *
     * @return void
     */
    public function testCliHandlesExecutionExceptions(): void
    {
        $bootstrap = (new \ReflectionClass(TestableBootstrap::class))->newInstanceWithoutConstructor();
        $bootstrap->mockCli = true;
        $bootstrap->setProviders([]);

        $container = $this->createMock(TestDiContainerInterface::class);
        $bootstrap->mockCliContainer = $container;

        $mockConsole = new class {
            public function handle(array $arguments): void
            {
                throw new \RuntimeException('Task failed fatally');
            }
        };
        $bootstrap->mockConsole = $mockConsole;

        $bootstrap->cli(['app/cli.php', 'failing', 'task']);

        $this->assertTrue($bootstrap->terminated);
        $this->assertSame(1, $bootstrap->exitCode);
        $this->assertStringContainsString('Task failed fatally', $bootstrap->cliErrorOutput);
    }
}
