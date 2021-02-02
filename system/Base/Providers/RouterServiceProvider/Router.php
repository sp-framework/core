<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Router as PhalconRouter;
use System\Base\Providers\RouterServiceProvider\Exceptions\AppNotAllowedException;
use System\Base\Providers\RouterServiceProvider\Exceptions\DomainNotRegisteredException;

class Router
{
	protected $domains;

	protected $domain;

	protected $domainAppExclusive = false;

	protected $domainDefaults;

	protected $apps;

	protected $components;

	protected $views;

	protected $logger;

	protected $request;

	protected $router;

	protected $appInfo;

	protected $appDefaults;

	protected $requestUri;

	protected $givenRouteClass = '';

	protected $defaultNamespace;

	protected $controller;

	protected $action;

	protected $loopCount;

	protected $uri;

	public function __construct($domains, $apps, $components, $views, $logger, $request)
	{
		$this->domains = $domains;

		$this->apps = $apps;

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

			if ($this->appInfo && $this->appDefaults) {
				if ($this->appInfo['route'] !== $this->appDefaults['app']) {
					if ($this->uri !== '' &&
						$this->uri !== strtolower($this->appInfo['route'])
					) {
						$this->registerRoute($this->uri);
					}
				} else {
					if ($this->uri !== '' &&
						$this->uri !== strtolower($this->appDefaults['app']) &&
						$this->uri !== strtolower($this->appInfo['route'])
					) {

						$this->registerRoute($this->uri);

					} else if ($this->uri === '' ||
							   $this->uri === strtolower($this->appDefaults['app']) ||
							   $this->uri === strtolower($this->appInfo['route'])
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
				'Apps\\' .
				ucfirst($this->appDefaults['app_type']) .
				'\\Components\\' .
				ucfirst($this->appDefaults['component'])
				;
		} else {
			if ($this->givenRouteClass !== '') {
				$this->defaultNamespace =
					'Apps\\' .
					ucfirst($this->appDefaults['app_type']) .
					'\\Components' .
					$this->givenRouteClass . '\\' .
					ucfirst($this->controller)
					;
			} else {
				$this->defaultNamespace =
					'Apps\\' .
					ucfirst($this->appDefaults['app_type']) .
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
				'controller'	=> 	strtolower($this->appDefaults['component']),
				'action'		=> 'view'
			]
		);

		$this->router->add(
			'/' . strtolower($this->appInfo['route']),
			[
				'controller'	=> 	strtolower($this->appDefaults['component']),
				'action'		=> 'view'
			]
		);

		$this->router->add(
			'/' . strtolower($this->appInfo['route']) . '/',
			[
				'controller'	=> 	strtolower($this->appDefaults['component']),
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
			unset($routeArray[0]); //Remove app name
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
		if ($this->appDefaults) {
			if (isset($this->appDefaults['errorComponent'])) {

				$errorComponent = ucfirst($this->appDefaults['errorComponent']);

			} else {

				$errorComponent = 'Errors';
			}

		} else {
			$errorComponent = 'Errors';
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

		if (isset($this->domain['exclusive_to_default_app']) &&
			$this->domain['exclusive_to_default_app'] == 1
		) {
			$this->appInfo = $this->apps->getIdApp($this->domain['default_app_id']);

			$this->domainAppExclusive = true;

		} else  {
			$this->appInfo = $this->apps->getAppInfo();

			if (!$this->appInfo) {
				return false;
			}

			if ((isset($this->domain['apps'][$this->appInfo['id']]['allowed']) &&
				!$this->domain['apps'][$this->appInfo['id']]['allowed']) ||
				!isset($this->domain['apps'][$this->appInfo['id']])
			) {
				$this->logger->log->alert(
					'Trying to access app ' . $this->appInfo['name'] .
					' on domain ' . $this->request->getHttpHost()
				);
				throw new AppNotAllowedException('Trying to access app ' . $this->appInfo['name'] .
					' on domain ' . $this->request->getHttpHost());
			}
		}

		$this->appDefaults['id'] = $this->appInfo['id'];
		$this->appDefaults['app'] = $this->appInfo['route'];
		$this->appDefaults['app_type'] = $this->appInfo['app_type'];
		$this->appDefaults['component'] =
			$this->components->getComponentById($this->appInfo['default_component'])['route'];
		$this->appDefaults['errorComponent'] =
			isset($this->appInfo['errors_component']) && $this->appInfo['errors_component'] != 0 ?
			$this->components->getComponentById($this->appInfo['errors_component'])['route'] :
			null;
		$this->appDefaults['view'] =
			$this->views->getIdViews($this->domain['apps'][$this->appInfo['id']]['view'])['name'];

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