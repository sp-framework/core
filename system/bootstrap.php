<?php

namespace System;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use System\Base\Exceptions\Handler;
use System\Base\Loader\Service;
use System\Base\Providers\ConfigServiceProvider;
use System\Base\Providers\LoggerServiceProvider;
use System\Base\Providers\SessionServiceProvider;

$container = new FactoryDefault();

include('../system/Base/Loader/Service.php');
Service::Instance($container, __DIR__ . '/../')->load();

foreach (include(base_path('system/Base/Providers.php')) as $provider) {
	$container->register(new $provider());
}

$session = $container->getShared('session');
$session->start();

$logger = $container->getShared('logger');

$logger->log->info('Session Start');

$application = new Application($container);

try {
	$response = $application->handle($_SERVER["REQUEST_URI"]);

	$logger->log->debug('Dispatched');
} catch (\Exception $e) {

	$logger->log->emergency(
		"Bootstrap Errors: " . get_class($e) . "<br>" .
		"Info: " . $e->getMessage() . "<br>" .
		"File: " . $e->getFile() . "<br>" .
		"Line: " . $e->getLine() . "<br>"
	);

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

	$logger->log->debug('Response Sent. Session End');

} else {
	echo $response->getContent();

	$logger->log->debug('Response Echoed. Session End');
}

$logger->log->info('Session End');

$logger->commit();