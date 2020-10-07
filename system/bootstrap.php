<?php

namespace System;

use Phalcon\Di\FactoryDefault;
use System\Base\Providers\ConfigServiceProvider;
use System\Base\Loader\Service;
use Phalcon\Mvc\Application;

include('../system/Base/Providers/ConfigServiceProvider.php');

$container = new FactoryDefault();
$container->register(new ConfigServiceProvider());
$config = $container->getShared('config');

include('../system/Base/Loader/Service.php');
$loader = Service::Instance($container, __DIR__ . '/../');
$loader->load();

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