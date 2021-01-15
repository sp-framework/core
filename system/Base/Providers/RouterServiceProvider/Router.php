<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Router as PhalconRouter;
use System\Base\Providers\RouterServiceProvider\Exceptions\ApplicationNotAllowedException;
use System\Base\Providers\RouterServiceProvider\Exceptions\DomainNotRegisteredException;

class Router
{
	protected $domains;

	protected $domain;

	protected $domainAppExclusive = false;

	protected $domainDefaults;

	protected $applications;

	protected $components;

	protected $views;

	protected $logger;

	protected $request;

	protected $router;

	protected $applicationInfo;

	protected $applicationDefaults;

	protected $requestUri;

	protected $givenRouteClass = '';

	protected $defaultNamespace;

	protected $controller;

	protected $action;

	protected $loopCount;

	protected $uri;

	public function __construct($domains, $applications, $components, $views, $logger, $request)
	{
		$this->domains = $domains;

		$this->applications = $applications;

		$this->components = $components;

		$this->views = $views;

		$this->logger = $logger;

		$this->request = $request;

		$this->requestUri = $this->request->getURI();

		$this->router = new PhalconRouter(false);

		$this->router->removeExtraSlashes(true);
	}

	public function init()
	{
		if ($this->validateDomain()) {

			$this->getURI();

			if ($this->applicationInfo && $this->applicationDefaults) {
				if ($this->applicationInfo['route'] !== $this->applicationDefaults['application']) {
					if ($this->uri !== '' &&
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

	protected function setDefaultNamespace(bool $home = false)
	{
		if ($home) {
			$this->defaultNamespace =
				'Applications\\' .
				ucfirst($this->applicationDefaults['app_type']) .
				// '\\' .
				// ucfirst($this->applicationDefaults['sub_category']) .
				'\\Components\\' .
				ucfirst($this->applicationDefaults['component'])
				;
		} else {
			if ($this->givenRouteClass !== '') {
				$this->defaultNamespace =
					'Applications\\' .
					ucfirst($this->applicationDefaults['app_type']) .
					// '\\' .
					// ucfirst($this->applicationDefaults['sub_category']) .
					'\\Components' .
					$this->givenRouteClass . '\\' .
					ucfirst($this->controller)
					;
			} else {
				$this->defaultNamespace =
					'Applications\\' .
					ucfirst($this->applicationDefaults['app_type']) .
					// '\\' .
					// ucfirst($this->applicationDefaults['sub_category']) .
					'\\Components\\' .
					$this->givenRouteClass .
					ucfirst($this->controller)
					;
			}
		}

		$this->router->setDefaultNamespace($this->defaultNamespace);
	}

	protected function registerHome()
	{
		$this->setDefaultNamespace(true);

		$this->router->add(
			'/',
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

		$this->router->add(
			'/' . strtolower($this->applicationInfo['route']) . '/',
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

		$this->setDefaultNamespace(false);

		$this->router->add(
			$routeToMatch,
			[
				'namespace' 	=> $this->defaultNamespace,
				'controller'	=> $this->controller,
				'action'		=> $this->action,
				'params'		=> isset($this->getQuery) ? 1 : null
			]
		);
	}

	protected function getGivenRouteClass(array $routeArray)
	{
		if (!$this->domainAppExclusive) {
			unset($routeArray[0]); //Remove application name
		}

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
			'System\Base\Providers\ErrorServiceProvider'
		);

		$this->router->setDefaultController('index');

		$this->router->setDefaultAction('view');
	}

	protected function regitserNotFound()
	{
		if ($this->applicationDefaults) {
			if (isset($this->applicationDefaults['errorComponent'])) {

				$errorComponent = ucfirst($this->applicationDefaults['errorComponent']);

			} else {

				$errorComponent = 'Errors';
			}

		} else {
			// $this->router->setDefaultNamespace
			// (
			// 	'System\Base\Providers\ErrorServiceProvider'
			// );

			$errorComponent = 'Errors';
			// $errorComponent = 'Errors';
		}

		$this->router->notFound(
			[
				'controller' => $errorComponent,
				'action'     => 'routeNotFound',
			]
		);
	}

	protected function validateDomain()
	{
		$this->domain = $this->domains->getDomain();
		if (!$this->domain) {
			$this->logger->log->alert(
				'Domain ' . $this->request->getHttpHost() . ' is not registered with system!'
			);

			throw new DomainNotRegisteredException('Domain ' . $this->request->getHttpHost() . ' is not registered with system!');
		}

		if (isset($this->domain['exclusive_to_default_application']) &&
			$this->domain['exclusive_to_default_application'] == 1
		) {
			$this->applicationInfo = $this->applications->getIdApplication($this->domain['default_application_id']);

			$this->domainAppExclusive = true;

		} else  {
			$this->applicationInfo = $this->applications->getApplicationInfo();

			if (!$this->applicationInfo) {
				return false;
			}

			if ((isset($this->domain['applications'][$this->applicationInfo['id']]['allowed']) &&
				!$this->domain['applications'][$this->applicationInfo['id']]['allowed']) ||
				!isset($this->domain['applications'][$this->applicationInfo['id']])
			) {
				$this->logger->log->alert(
					'Trying to access application ' . $this->applicationInfo['name'] .
					' on domain ' . $this->request->getHttpHost()
				);
				throw new ApplicationNotAllowedException('Trying to access application ' . $this->applicationInfo['name'] .
					' on domain ' . $this->request->getHttpHost());
			}
		}

		$this->applicationDefaults['id'] = $this->applicationInfo['id'];
		$this->applicationDefaults['application'] = $this->applicationInfo['route'];
		$this->applicationDefaults['app_type'] = $this->applicationInfo['app_type'];
		// $this->applicationDefaults['category'] = $this->applicationInfo['category'];
		// $this->applicationDefaults['sub_category'] = $this->applicationInfo['sub_category'];
		$this->applicationDefaults['component'] =
			$this->components->getIdComponent($this->applicationInfo['default_component'])['route'];
		$this->applicationDefaults['errorComponent'] =
			isset($this->applicationInfo['errors_component']) && $this->applicationInfo['errors_component'] != 0 ?
			$this->components->getIdComponent($this->applicationInfo['errors_component'])['route'] :
			null;
		$this->applicationDefaults['view'] =
			$this->views->getIdViews($this->domain['applications'][$this->applicationInfo['id']]['view'])['name'];

		return true;
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