<?php

return
	[
		'mvc'	=>
			[
				'System\Base\Providers\ConfigServiceProvider',
				'System\Base\Providers\SupportServiceProvider',
				'System\Base\Providers\EventsServiceProvider',
				'System\Base\Providers\AnnotationsServiceProvider',
				'System\Base\Providers\SecurityServiceProvider',
				// 'System\Base\Providers\SessionServiceProvider',
				'System\Base\Providers\DatabaseServiceProvider',
				'System\Base\Providers\CacheServiceProvider',
				'System\Base\Providers\BasepackagesServiceProvider',
				'System\Base\Providers\CoreServiceProvider',
				'System\Base\Providers\AppsServiceProvider',
				'System\Base\Providers\DomainsServiceProvider',
				'System\Base\Providers\ModulesServiceProvider',
				// 'System\Base\Providers\HttpServiceProvider',
				'System\Base\Providers\LoggerServiceProvider',
				'System\Base\Providers\ContentServiceProvider',
				'System\Base\Providers\RouterServiceProvider',
				'System\Base\Providers\DispatcherServiceProvider',
				'System\Base\Providers\ViewServiceProvider',
				'System\Base\Providers\FlashServiceProvider',
				'System\Base\Providers\AccessServiceProvider',
				'System\Base\Providers\ErrorServiceProvider',
				'System\Base\Providers\WidgetServiceProvider',
				'System\Base\Providers\ValidationServiceProvider',
				'System\Base\Providers\WebSocketServiceProvider',
			],
		'cli'	=>
			[
				'System\Base\Providers\ConfigServiceProvider',
				'System\Base\Providers\SupportServiceProvider',
				'System\Base\Providers\EventsServiceProvider',
				'System\Base\Providers\HttpServiceProvider',
				// 'System\Base\Providers\AnnotationsServiceProvider',
				'System\Base\Providers\SecurityServiceProvider',
				'System\Base\Providers\SessionServiceProvider',
				'System\Base\Providers\DatabaseServiceProvider',
				'System\Base\Providers\CacheServiceProvider',
				'System\Base\Providers\BasepackagesServiceProvider',
				'System\Base\Providers\CoreServiceProvider',
				'System\Base\Providers\AppsServiceProvider',
				'System\Base\Providers\DomainsServiceProvider',
				'System\Base\Providers\ModulesServiceProvider',
				'System\Base\Providers\LoggerServiceProvider',
				'System\Base\Providers\ContentServiceProvider',
				// 'System\Base\Providers\RouterServiceProvider',
				// 'System\Base\Providers\DispatcherServiceProvider',
				// 'System\Base\Providers\ViewServiceProvider',
				'System\Base\Providers\WebSocketServiceProvider',
				// 'System\Base\Providers\FlashServiceProvider',
				// 'System\Base\Providers\ErrorServiceProvider',
				// 'System\Base\Providers\WidgetServiceProvider',
				'System\Base\Providers\ValidationServiceProvider',
				// 'System\Base\Providers\AccessServiceProvider',
			],
		'api'	=>
			[
				'System\Base\Providers\ConfigServiceProvider',
				'System\Base\Providers\DatabaseServiceProvider',
				'System\Base\Providers\ContentServiceProvider',
				'System\Base\Providers\BasepackagesServiceProvider',
				'System\Base\Providers\RouterServiceProvider',
				'System\Base\Providers\DomainsServiceProvider',
				'System\Base\Providers\AppsServiceProvider',
				'System\Base\Providers\CoreServiceProvider',
				// 'System\Base\Providers\SessionServiceProvider',
				// 'System\Base\Providers\SupportServiceProvider',
				// 'System\Base\Providers\EventsServiceProvider',
				// 'System\Base\Providers\AnnotationsServiceProvider',
				// 'System\Base\Providers\SecurityServiceProvider',
				// 'System\Base\Providers\CacheServiceProvider',
				// 'System\Base\Providers\ModulesServiceProvider',
				// 'System\Base\Providers\HttpServiceProvider',
				'System\Base\Providers\LoggerServiceProvider',
				// 'System\Base\Providers\DispatcherServiceProvider',
				// 'System\Base\Providers\ViewServiceProvider',
				// 'System\Base\Providers\FlashServiceProvider',
				// 'System\Base\Providers\AccessServiceProvider',
				// 'System\Base\Providers\ErrorServiceProvider',
				// 'System\Base\Providers\WidgetServiceProvider',
				// 'System\Base\Providers\ValidationServiceProvider',
				// 'System\Base\Providers\WebSocketServiceProvider',
			]
	];