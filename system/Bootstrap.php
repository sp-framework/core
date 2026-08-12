<?php

/**
 * SP Framework
 *
 * @package   System
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace System;

use Phalcon\Cli\Console;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Micro;
use System\Base\Loader\Service;
use System\Base\Providers\ApiServiceProvider;
use System\Base\Providers\EventsServiceProvider\MicroEvents;
use System\Base\Providers\RouterServiceProvider\MicroCollection;
use System\Base\Providers\SessionServiceProvider;

/**
 * Class Bootstrap
 *
 * The core application bootstrapper responsible for initializing the dependency
 * injection container, registering service providers, configuring runtime environment,
 * and dispatching requests across MVC, Micro/API, and CLI execution modes.
 *
 * @package System
 */
class Bootstrap
{
    /**
     * Map of service provider class names categorized by execution mode ('mvc', 'cli', 'api').
     *
     * @var array|null
     */
    protected ?array $providers = null;

    /**
     * The registered error handling service instance.
     *
     * @var object|null
     */
    public ?object $error = null;

    /**
     * The registered logger service instance.
     *
     * @var object|null
     */
    public ?object $logger = null;

    /**
     * Flag indicating whether the current request is an API request.
     *
     * @var bool
     */
    public bool $isApi = false;

    /**
     * The application configuration object.
     *
     * @var object|null
     */
    public ?object $config = null;

    /**
     * The HTTP response service instance.
     *
     * @var object|null
     */
    public ?object $response = null;

    /**
     * Bootstrap constructor.
     *
     * Initializes the class loader service and loads provider configurations.
     *
     * @param string|null $basePath  Optional custom base path for system root directory.
     * @param array|null  $providers Optional pre-loaded providers array.
     * @param bool        $autoload  Flag controlling whether to invoke the internal class loader service.
     */
    public function __construct(?string $basePath = null, ?array $providers = null, bool $autoload = true)
    {
        $base = $basePath ?? (__DIR__ . '/../');

        if ($autoload) {
            if (!class_exists(Service::class, false)) {
                $serviceLoader = $base . 'system/Base/Loader/Service.php';

                if (file_exists($serviceLoader)) {
                    include_once $serviceLoader;
                }
            }

            if (class_exists(Service::class)) {
                Service::Instance($base)->load();
            }
        }

        if ($providers !== null) {
            $this->providers = $providers;
        } else {
            $providersFile = function_exists('base_path')
                ? base_path('system/Base/Providers.php')
                : $base . 'system/Base/Providers.php';

            if (file_exists($providersFile)) {
                $this->providers = include $providersFile;
            }
        }
    }

    /**
     * Gets the configured service providers array.
     *
     * @return array|null
     */
    public function getProviders(): ?array
    {
        return $this->providers;
    }

    /**
     * Sets the service providers array.
     *
     * @param array $providers Associative array with 'mvc', 'cli', and 'api' provider lists.
     *
     * @return self
     */
    public function setProviders(array $providers): self
    {
        $this->providers = $providers;

        return $this;
    }

    /**
     * Executes the HTTP application lifecycle for MVC and Micro/API requests.
     *
     * Initializes the DI container, determines the execution mode (API vs MVC),
     * registers mode-specific service providers, sets up environment configurations, and handles
     * the incoming HTTP request.
     *
     * @return void
     */
    public function mvc(): void
    {
        if ($this->isCli()) {
            echo "Cannot use cli on index.php";

            $this->terminate(1);

            return;
        }

        ini_set('zlib.output_compression', '1');

        $container = $this->initContainer();

        $this->registerBaseServices($container);

        $this->isApi = $this->determineExecutionMode($container);

        $providers = $this->isApi
            ? ($this->providers['api'] ?? [])
            : ($this->providers['mvc'] ?? []);

        $this->registerProviders($container, $providers);

        $this->config = $container->getShared('config');

        $this->configureEnvironment($container);

        $this->response = $container->getShared('response');

        $this->logger = $container->getShared('logger');

        if ($this->isApi) {
            $this->handleApiRequest($container);
        } else {
            $this->handleMvcRequest($container);
        }
    }

    /**
     * Executes the Command Line Interface (CLI) application lifecycle.
     *
     * Initializes the CLI DI container, registers CLI service providers, configures the task
     * dispatcher namespace, parses command line arguments, and dispatches the console task.
     *
     * @param array $argv Command line arguments passed to the script.
     *
     * @return void
     */
    public function cli(array $argv): void
    {
        if (!$this->isCli()) {
            echo "Cannot use anything other than cli on cli.php";

            $this->terminate(1);

            return;
        }

        $container = $this->initCliContainer();

        $providers = $this->providers['cli'] ?? [];

        $this->registerProviders($container, $providers);

        $this->logger = $container->getShared('logger');

        $dispatcher = $container->getShared('dispatcher');

        if ($dispatcher && method_exists($dispatcher, 'setDefaultNamespace')) {
            $dispatcher->setDefaultNamespace('System\Cli\Tasks');
        }

        $arguments = $this->parseCliArguments($argv);

        $console = $this->initConsole($container);

        try {
            $console->handle($arguments);

            $this->logger?->commit();
        } catch (\Throwable $throwable) {
            $this->writeCliError($throwable->getMessage() . PHP_EOL);

            $this->logger?->commit();

            $this->terminate(1);
        }
    }

    /**
     * Parses command line argument tokens into structured task, action, and parameters array.
     *
     * @param array $argv Raw $argv array from CLI invocation.
     *
     * @return array Associative array containing 'task', 'action', and 'params' keys.
     */
    public function parseCliArguments(array $argv): array
    {
        $arguments = [];

        foreach ($argv as $k => $arg) {
            if ($k === 1) {
                $arguments['task'] = $arg;
            } elseif ($k === 2) {
                $arguments['action'] = $arg;
            } elseif ($k >= 3) {
                $arguments['params'][] = $arg;
            }
        }

        return $arguments;
    }

    /**
     * Initializes the default DI container for HTTP web requests.
     *
     * @return DiInterface
     */
    protected function initContainer(): DiInterface
    {
        return new FactoryDefault();
    }

    /**
     * Initializes the DI container for CLI tasks.
     *
     * @return DiInterface
     */
    protected function initCliContainer(): DiInterface
    {
        return new Cli();
    }

    /**
     * Initializes the Console application for CLI task handling.
     *
     * @param DiInterface $container The DI container.
     *
     * @return object|Console
     */
    protected function initConsole(DiInterface $container): object
    {
        return new Console($container);
    }

    /**
     * Registers fundamental base services required for request type detection and session management.
     *
     * @param DiInterface $container The DI container.
     *
     * @return void
     */
    protected function registerBaseServices(DiInterface $container): void
    {
        $container->register(new SessionServiceProvider());

        $session = $container->getShared('session');

        $container->getShared('connection');

        if ($session && method_exists($session, 'start')) {
            $session->start();
        }

        $container->register(new ApiServiceProvider());
    }

    /**
     * Determines whether the current HTTP request targets the API endpoint.
     *
     * @param DiInterface $container The DI container.
     *
     * @return bool True if API request, false for standard MVC request.
     */
    protected function determineExecutionMode(DiInterface $container): bool
    {
        $api = $container->getShared('api');

        return (bool) ($api && method_exists($api, 'isApi') ? $api->isApi() : false);
    }

    /**
     * Registers an array of service provider classes into the DI container.
     *
     * @param DiInterface $container The DI container.
     * @param array       $providers List of service provider class names.
     *
     * @return void
     */
    protected function registerProviders(DiInterface $container, array $providers): void
    {
        foreach ($providers as $provider) {
            $container->register(new $provider());
        }
    }

    /**
     * Configures runtime environment options such as timezone, error reporting, and debug flags.
     *
     * @param DiInterface $container The DI container.
     *
     * @return void
     */
    protected function configureEnvironment(DiInterface $container): void
    {
        if (isset($this->config->locale->timezone)) {
            date_default_timezone_set($this->config->locale->timezone);
        }

        if (!empty($this->config->debug)) {
            ini_set('display_errors', '1');

            ini_set('display_startup_errors', '1');

            error_reporting(E_ALL);
        }
    }

    /**
     * Handles Micro/API request mounting, event binding, route execution, and logger commit.
     *
     * @param DiInterface $container The DI container.
     *
     * @return void
     */
    protected function handleApiRequest(DiInterface $container): void
    {
        $application = new Micro($container);

        $api = $container->getShared('api');
        $request = $container->getShared('request');
        $router = $container->getShared('router');
        $domains = $container->getShared('domains');
        $helper = $container->getShared('helper');

        $microCollection = (new MicroCollection($request, $application, $api, $router, $domains))->init();
        $application->mount($microCollection->getMicroCollection());

        $events = (new MicroEvents())->init();
        $application->setEventsManager($events);

        $uri = $helper->reduceSlashes($this->getRequestUri());
        $application->handle($uri);

        $this->logger?->commit();
    }

    /**
     * Handles full MVC application route resolution, response rendering, and logger commit.
     *
     * @param DiInterface $container The DI container.
     *
     * @return void
     */
    protected function handleMvcRequest(DiInterface $container): void
    {
        $this->error = $container->getShared('error');
        $helper = $container->getShared('helper');

        $application = new Application($container);

        $uri = $helper->reduceSlashes($this->getRequestUri());
        $response = $application->handle($uri);

        if (!$response->isSent()) {
            $response->send();
        } else {
            echo $response->getContent();
        }

        $this->logger?->commit();
    }

    /**
     * Checks whether the current execution context is CLI.
     *
     * @return bool True if running in CLI mode, false otherwise.
     */
    protected function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Terminates script execution with the specified exit status code.
     *
     * @param int $code Process exit status code (default: 0).
     *
     * @return void
     */
    protected function terminate(int $code = 0): void
    {
        exit($code);
    }

    /**
     * Writes an error message to the CLI error stream (STDERR) or standard output.
     *
     * @param string $message The formatted error string to output.
     *
     * @return void
     */
    protected function writeCliError(string $message): void
    {
        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $message);
        } else {
            echo $message;
        }
    }

    /**
     * Retrieves the current request URI from the server environment.
     *
     * @return string The request URI path.
     */
    protected function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}
