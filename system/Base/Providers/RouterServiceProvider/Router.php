<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Router as PhalconRouter;

class Router
{
	private $container;

	protected $router;

	protected $applications;

	protected $applicationInfo;

	protected $applicationDefaults;

	protected $request;

	protected $requestUri;

	protected $givenRouteClass = '';

	protected $defaultNamespace;

	protected $controller;

	protected $action;

	protected $loopCount;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->applications = $this->container->getShared('modules')->applications;

		$this->request = $this->container->getShared('request');

		$this->requestUri = $this->request->getURI();

		$this->router = new PhalconRouter(false);

		$this->router->removeExtraSlashes(true);
	}

	public function setRoute()
	{
		$this->setApplicationInfo();

		$this->defaultNamespace =
			'Applications\\' . ucfirst($this->applicationDefaults['application']) . '\\Components';

		$uri = explode('/q/', trim($this->requestUri, '/'));

		$this->getQuery = $uri[1] ?? null;

		if ($this->applicationInfo && $this->applicationDefaults) {
			if ($this->applicationInfo['name'] !== $this->applicationDefaults['application']) {
				if ($uri[0] !== '' &&
					$uri[0] !== strtolower($this->applicationInfo['name']) &&
					$uri[0] !== strtolower($this->applicationInfo['route'])
				) {

					$this->registerRoute($uri[0]);
				}
			} else {
				if ($uri[0] !== '' &&
					$uri[0] !== strtolower($this->applicationDefaults['application']) &&
					$uri[0] !== strtolower($this->applicationInfo['route'])
				) {

					$this->registerRoute($uri[0]);

				} else if ($uri[0] === '' ||
						   $uri[0] === strtolower($this->applicationDefaults['application']) ||
						   $uri[0] === strtolower($this->applicationInfo['route'])
				) {
					$this->registerHome();
				}
			}
		}

		$this->registerDefaults();

		$this->regitserNotFound();

		return $this->router;
	}

	protected function registerHome()
	{
		$this->router->setDefaultNamespace($this->defaultNamespace);
		$this->router->setDefaultController(strtolower($this->applicationDefaults['component']));
		$this->router->setDefaultAction('view');
	}

	protected function registerRoute($givenRoute)
	{
		$routeArray = explode('/', $givenRoute);

		$this->getGivenRouteClass($routeArray);

		if ($this->getQuery) {
			$routeToMatch = '/' . $givenRoute . '/q/' . ':params';
		} else {
			$routeToMatch = '/' . $givenRoute;
		}

		$this->router->add(
			$routeToMatch,
			[
				'namespace' 	=> $this->defaultNamespace . $this->givenRouteClass,
				'controller'	=> $this->controller,
				'action'		=> $this->action,
				'params'		=> isset($this->getQuery) ? 1 : null
			]
		);
	}

	protected function getGivenRouteClass(array $routeArray)
	{
		unset($routeArray[0]); //Remove application name

		if ($this->request->isGet()) {
			$this->controller = Arr::last($routeArray);
			$this->action = 'view';
			unset($routeArray[Arr::lastKey($routeArray)]);

			foreach ($routeArray as $route) {
				$this->givenRouteClass .= '\\' . ucfirst($route);
			}

		} elseif ($this->request->isPost()) {
			$this->action = Arr::last($routeArray);
			unset($routeArray[Arr::lastKey($routeArray)]);
			$this->controller = Arr::last($routeArray);
			unset($routeArray[Arr::lastKey($routeArray)]);
			foreach ($routeArray as $route) {
				$this->givenRouteClass .= '\\' . ucfirst($route);
			}
		}
	}

	protected function registerDefaults()
	{
		// This will be defined per application errors
		// $this->router->setDefaults();
	}

	protected function regitserNotFound()
	{
		// $this->router->notFound(
		// 	[
		// 		'controller' => $this->applicationDefaults['errorController'],
		// 		'action'     => 'view',
		// 	]
		// );
	}

	protected function setApplicationInfo()
	{
		$this->applicationInfo = $this->applications->getApplicationInfo();

		if ($this->applicationInfo) {

			$this->applicationDefaults = $this->applications->getApplicationDefaults();

			if (!$this->applicationDefaults ||
				$this->applicationInfo['name'] !== $this->applicationDefaults['application']
			) {
				$this->applicationDefaults =
					$this->applications->getApplicationDefaults($this->applicationInfo['name']);
			}
		}
	}
}