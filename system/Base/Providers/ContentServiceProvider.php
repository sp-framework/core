<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ContentServiceProvider\Local\Content as LocalContent;
use System\Base\Providers\ContentServiceProvider\RemoteWeb\Content as RemoteWebContent;
use System\Base\Providers\ContentServiceProvider\Remote\Content as RemoteContent;

class ContentServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'localContent',
			function () {
				return (new LocalContent())->init();
			}
		);

		$container->setShared(
			'remoteContent',
			function () {
				return (new RemoteContent())->init();
			}
		);

		$container->setShared(
			'remoteWebContent',
			function () {
				return (new RemoteWebContent())->init();
			}
		);
	}
}