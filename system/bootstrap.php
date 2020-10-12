<?php

namespace System;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use System\Base\Exceptions\Handler;
use System\Base\Loader\Service;
use System\Base\Providers\ConfigServiceProvider;
use System\Base\Providers\SessionServiceProvider;

$container = new FactoryDefault();
include('../system/Base/Providers/SessionServiceProvider.php');
$container->register(new SessionServiceProvider());
$session = $container->getShared('session');

$session->start();

include('../system/Base/Providers/ConfigServiceProvider.php');

$container->register(new ConfigServiceProvider());
$config = $container->getShared('config');

include('../system/Base/Loader/Service.php');
$loader = Service::Instance($container, __DIR__ . '/../');
$loader->load();

foreach ($config->providers as $provider) {
	$container->register(new $provider());
}

$application = new Application($container);

try {
	$response = $application->handle($_SERVER["REQUEST_URI"]);
} catch (\Exception $e) {
	$handler = new Handler(
		$e,
		$container->getShared('session'),
		$container->getShared('response'),
		$container->getShared('view'),
		$container->getShared('flashSession')
	);

	$response = $handler->respond();
}

if (!$response->isSent()) {
	$response->send();
} else {
	echo $response->getContent();
}