<?php
declare(strict_types=1);

namespace System;

use Exception;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use System\Base\Loader\Service;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Processes;
use System\Base\Providers\CacheServiceProvider\CacheTools;
use System\Base\Providers\CacheServiceProvider\StreamCache;
use Throwable;

if (PHP_SAPI !== 'cli') {
    echo "Cannot use anything other than cli on cli.php";
    exit();
}
include(__DIR__ . '/../system/Base/Loader/Service.php');
Service::Instance(__DIR__ . '/../')->load();

$container  = new CliDI();

$providers = include(base_path('system/Base/Providers.php'));
foreach ($providers['cli'] as $provider) {
    $container->register(new $provider());
}

$dispatcher = $container->getShared('dispatcher');
$dispatcher->setDefaultNamespace('System\Cli\Tasks');

// var_dump($container);die();
// $processes = (new Processes())->init();
// $container->setShared('processes', $processes);

$console = new Console($container);

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

try {
    $console->handle($arguments);
} catch (PhalconException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}