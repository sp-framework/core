<?php

namespace System;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Exception as PhalconException;
use Phalcon\Mvc\Application;
use System\Base\Loader\Service;
use System\Base\Providers\SessionServiceProvider;

final class Bootstrap
{
    protected $providers;

    public $error;

    public $logger;

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

        $container->register(new SessionServiceProvider());
        $session = $container->getShared('session');
        $connection = $container->getShared('connection');
        $session->start();

        foreach ($this->providers['mvc'] as $provider) {
            $container->register(new $provider());
        }

        $this->error = $container->getShared('error');
        $this->logger = $container->getShared('logger');

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
        } catch (PhalconException $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        } catch (\Throwable $throwable) {
            fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
            exit(1);
        } catch (\Exception $exception) {
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}