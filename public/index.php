<?php
/**
 * SP Framework Application Entry Point
 *
 * This file serves as the single bootstrap and execution gateway for all incoming
 * HTTP (MVC and Micro/API) requests and Command Line Interface (CLI) tasks.
 *
 * @package    	System
 * @copyright   Copyright (c) 2026
 * @link       	https://github.com/sp-framework/core
 */

use System\Base\Providers\ErrorServiceProvider\BootstrapExceptionHandler;
use System\Bootstrap;

try {
	require_once __DIR__ . '/../system/Bootstrap.php';

	$bootstrap = new Bootstrap();

	if (PHP_SAPI === 'cli') {
		$bootstrap->cli($argv ?? $_SERVER['argv'] ?? []);
	} else {
		$bootstrap->mvc();
	}
} catch (\Throwable $exception) {
	require_once __DIR__ . '/../system/Base/Providers/ErrorServiceProvider/BootstrapExceptionHandler.php';

	(new BootstrapExceptionHandler())->handle($exception, $bootstrap ?? null);
}