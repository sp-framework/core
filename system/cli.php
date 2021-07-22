<?php
declare(strict_types=1);

namespace System;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use System\Base\Loader\Service;

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