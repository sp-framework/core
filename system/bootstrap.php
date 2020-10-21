<?php

namespace System;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use System\Base\Loader\Service;
use System\Base\Providers\SessionServiceProvider;

include('../system/Base/Loader/Service.php');
Service::Instance(__DIR__ . '/../')->load();

$container = new FactoryDefault();

include('../system/Base/Providers/SessionServiceProvider.php');
$container->register(new SessionServiceProvider());
$session = $container->getShared('session');
$connection = $container->getShared('connection');
$session->start();

foreach (include(base_path('system/Base/Providers.php')) as $provider) {
	$container->register(new $provider());
}

$error = $container->getShared('error');
$logger = $container->getShared('logger');

$logger->log->info(
	'Session ID: ' . $session->getId() . '. Connection ID: ' . $connection->getId()
);

$application = new Application($container);

$response = $application->handle($_SERVER["REQUEST_URI"]);

$logger->log->debug('Dispatched');

if (!$response->isSent()) {
	$response->send();

	$logger->log->debug('Response Sent.');

} else {
	echo $response->getContent();

	$logger->log->debug('Response Echoed.');
}

$logger->log->info('Session End');

$logger->commit();