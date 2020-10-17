<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Router as PhalconRouter;

class Router
{
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

	protected $uri;

	public function __construct($domains, $applications, $logger, $request)
	{
		$this->domains = $domains;

		$this->applications = $applications;

		$this->logger = $logger;

		$this->request = $request;

		$this->requestUri = $this->request->getURI();

		$this->router = new PhalconRouter(false);

		$this->router->removeExtraSlashes(true);
	}

	public function init()
	{
		if ($this->setApplicationInfo()) {
			$this->defaultNamespace =
				'Applications\\' . ucfirst($this->applicationDefaults['application']) . '\\Components';

			$this->router->setDefaultNamespace($this->defaultNamespace);

			$this->getURI();

			if ($this->applicationInfo && $this->applicationDefaults) {
				if ($this->applicationInfo['name'] !== $this->applicationDefaults['application']) {
					if ($this->uri !== '' &&
						$this->uri !== strtolower($this->applicationInfo['name']) &&
						$this->uri !== strtolower($this->applicationInfo['route'])
					) {
						$this->registerRoute($this->uri);
					}
				} else {
					if ($this->uri !== '' &&
						$this->uri !== strtolower($this->applicationDefaults['application']) &&
						$this->uri !== strtolower($this->applicationInfo['route'])
					) {

						$this->registerRoute($this->uri);

					} else if ($this->uri === '' ||
							   $this->uri === strtolower($this->applicationDefaults['application']) ||
							   $this->uri === strtolower($this->applicationInfo['route'])
					) {
						$this->registerHome();
					}
				}
			}
		} else {
			$this->registerDefaults();
		}

		$this->regitserNotFound();

		return $this->router;
	}

	protected function registerHome()
	{
		$this->router->add(
			'/',
			[
				'controller'	=> 	strtolower($this->applicationDefaults['component']),
				'action'		=> 'view'
			]
		);

		$this->router->add(
			'/' . strtolower($this->applicationInfo['name']),
			[
				'controller'	=> 	strtolower($this->applicationDefaults['component']),
				'action'		=> 'view'
			]
		);

		$this->router->add(
			'/' . strtolower($this->applicationInfo['route']),
			[
				'controller'	=> 	strtolower($this->applicationDefaults['component']),
				'action'		=> 'view'
			]
		);
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
		$this->router->setDefaultNamespace(
			'System\Base\Exceptions'
		);

		$this->router->setDefaultController('index');

		$this->router->setDefaultAction('view');
	}

	protected function regitserNotFound()
	{
		if ($this->applicationDefaults) {

			$settings = Json::decode($this->applicationInfo['settings'], true);

			if (isset($settings['errorComponent'])) {

				$errorComponent = $settings['errorComponent'];

			} else {
				$this->router->setDefaultNamespace
				(
					'System\Base\Exceptions'
				);

				$errorComponent = 'Errors';
			}

		} else {
			$this->router->setDefaultNamespace
			(
				'System\Base\Exceptions'
			);

			$errorComponent = 'Errors';
		}

		$this->router->notFound(
			[
				'controller' => $errorComponent,
				'action'     => 'notfound',
			]
		);
	}

	protected function setApplicationInfo()
	{
		$this->applicationInfo = $this->applications->getApplicationInfo();

		if (!$this->validateDomain()) {
			return false;
		}

		if ($this->applicationInfo) {

			$this->applicationDefaults = $this->applications->getApplicationDefaults();

			if (!$this->applicationDefaults ||
				$this->applicationInfo['name'] !== $this->applicationDefaults['application']
			) {
				$this->applicationDefaults =
					$this->applications->getApplicationDefaults($this->applicationInfo['name']);
			}
			return true;
		}
		return false;
	}

	protected function validateDomain()
	{
		$applicationDomain = Json::decode($this->applicationInfo['settings'], true);

		if (isset($applicationDomain['domain']) &&
				($applicationDomain['domain'] !== '' && $applicationDomain['domain'] !== '0')
		) {
			if (!$this->domains->getNamedDomain($this->request->getHttpHost())) {
				$this->logger->log->alert(
					'Domain ' . $this->request->getHttpHost() . ' is not registered with system!'
				);

				return false;
			}

			if ($this->request->getHttpHost() === $applicationDomain['domain']) {
				return true;
			} else {
				$this->logger->log->alert(
					'Trying to access application ' . $this->applicationInfo['name'] .
					' on domain ' . $this->request->getHttpHost() . '. Application is restricted to ' .
					$applicationDomain['domain']
				);

				return false;
			}
		} else {
			return true;
		}
	}

	protected function getURI()
	{
		if (!$this->uri) {
			$uri = explode('/q/', trim($this->requestUri, '/'));

			$this->uri = $uri[0];

			$this->getQuery = $uri[1] ?? null;
		}

		return $this->uri;
	}
}