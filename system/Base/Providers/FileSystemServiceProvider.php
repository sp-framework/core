<?php

namespace System\Base\Providers;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ConfigServiceProvider\Config;

class FileSystemServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'fileSystem',
			function () use ($container) {
				return new Filesystem(new Local(base_path()));
			}
		);
	}
}
