<?php

namespace System;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Micro;
use System\Base\Loader\Service;
use System\Base\Providers\ApiServiceProvider;
use System\Base\Providers\ErrorServiceProvider\MicroExceptionHandler;
use System\Base\Providers\EventsServiceProvider\MicroEvents;
use System\Base\Providers\HttpServiceProvider;
use System\Base\Providers\RouterServiceProvider\MicroCollection;
use System\Base\Providers\SessionServiceProvider;

final class Bootstrap
{
    protected $providers;

    public $error;

    public $logger;

    public $isApi;

    public $config;

    public $response;

    public function __construct()
    {
        include(__DIR__ . '/../system/Base/Loader/Service.php');

        Service::Instance(__DIR__ . '/../')->load();

        $this->providers = include(base_path('system/Base/Providers.php'));
    }

    public function mvc()
    {
        if (PHP_SAPI === 'cli') {
            echo "Cannot use cli on index.php";
            exit();
        }

        ini_set('zlib.output_compression', 1);

        $container = new FactoryDefault();

        include('../system/Base/Providers/SessionServiceProvider.php');
        include('../system/Base/Providers/HttpServiceProvider.php');
        include('../system/Base/Providers/ApiServiceProvider.php');

        $container->register(new SessionServiceProvider());
        $session = $container->getShared('session');
        $connection = $container->getShared('connection');
        $session->start();

        $container->register(new ApiServiceProvider());
        $api = $container->getShared('api');
        $this->isApi = $api->isApi();

        if ($this->isApi) {
            $providers = $this->providers['api'];
        } else {
            $providers = $this->providers['mvc'];
        }

        foreach ($providers as $provider) {
            $container->register(new $provider());
        }

        $this->config = $container->getShared('config');

        if ($this->config->debug) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        $this->response = $container->getShared('response');
        $this->logger = $container->getShared('logger');

        if ($this->isApi) {
            $application = new Micro($container);

            $request = $container->getShared('request');
            $router = $container->getShared('router');
            $domains = $container->getShared('domains');

            $microCollection = (new MicroCollection($request, $application, $api, $router, $domains))->init();
            $application->mount($microCollection->getMicroCollection());

            $events = (new MicroEvents())->init();

            $application->setEventsManager($events);

            $response = $application->handle($_SERVER["REQUEST_URI"]);

            $this->logger->commit();
        } else {
            $this->error = $container->getShared('error');

            $this->logger->log->info(
                'Session ID: ' . $session->getId() . '. Connection ID: ' . $connection->getId()
            );

            $application = new Application($container);

            $response = $application->handle($_SERVER["REQUEST_URI"]);

            $this->logger->log->debug('Dispatched');

            if (!$response->isSent()) {
                $response->send();

                $this->logger->log->debug('Response Sent.');

            } else {
                echo $response->getContent();

                $this->logger->log->debug('Response Echoed.');
            }

            $this->logger->log->info('Session End');

            $this->logger->commit();
        }
    }

    public function cli($argv)
    {
        if (PHP_SAPI !== 'cli') {
            echo "Cannot use anything other than cli on cli.php";
            exit();
        }

        $container  = new Cli();

        foreach ($this->providers['cli'] as $provider) {
            $container->register(new $provider());
        }

        $this->logger = $container->getShared('logger');
        $dispatcher = $container->getShared('dispatcher');
        $dispatcher->setDefaultNamespace('System\Cli\Tasks');

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

        $console = new Console($container);

        try {
            $console->handle($arguments);

            $this->logger->commit();
        } catch (\Exception $exception) {
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);

            $this->logger->commit();

            exit(1);
        } catch (\Throwable $throwable) {
            fwrite(STDERR, $throwable->getMessage() . PHP_EOL);

            $this->logger->commit();

            exit(1);
        }
    }
}