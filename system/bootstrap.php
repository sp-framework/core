<?php

namespace System;

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use System\Base\Providers\ConfigServiceProvider;
use Phalcon\Mvc\Application;


$namespaces = include('../system/Base/Loaders/Namespaces.php');
$files = include('../system/Base/Loaders/Files.php');

$loader = new Loader();
$loader->registerNamespaces($namespaces);
$loader->registerFiles($files);
$loader->register();

$container = new FactoryDefault();
$container->register(new ConfigServiceProvider());
$config = $container->getShared('config');

foreach ($config->providers as $provider) {
    $container->register(new $provider());
}

$application = new Application($container);

$response = $application->handle(
    $_SERVER["REQUEST_URI"]
);

if (!$response->isSent()) {
    $response->send();
} else {
    echo $response->getContent();
}