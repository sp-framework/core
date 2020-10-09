<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\FileSystemServiceProvider\File;

class FileSystemServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'fileSystem',
			function () use ($container) {
				return (new File($container))->init();
			}
		);
	}
}
