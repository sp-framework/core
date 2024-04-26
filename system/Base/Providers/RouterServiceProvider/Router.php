<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Helper\Arr;
use Phalcon\Mvc\Router as PhalconRouter;
use System\Base\Providers\RouterServiceProvider\Exceptions\AppNotAllowedException;
use System\Base\Providers\RouterServiceProvider\Exceptions\DomainNotRegisteredException;

class Router
{
	protected $api;

	protected $isApi;

	protected $isApiPublic;

	protected $domainApiExclusive = false;

	protected $domains;

	protected $domain;

	protected $domainAppExclusive = false;

	protected $domainDefaults;

	protected $apps;

	protected $components;

	protected $views;

	protected $logger;

	protected $request;

	protected $response;

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

	protected $getQuery;

	protected $helper;

	protected $config;

	protected $basepackages;

	protected $dispatcher;

	public function __construct(
		$api,
		$domains,
		$apps,
		$components,
		$views,
		$logger,
		$request,
		$response,
		$helper,
		$config,
		$basepackages,
		$dispatcher
	) {
		$this->api = $api;

		$this->domains = $domains;

		$this->apps = $apps;

		$this->components = $components;

		$this->views = $views;

		$this->logger = $logger;

		$this->request = $request;

		$this->isApi = $this->api->isApi();

		if ($this->api->isApiCheckVia && $this->api->isApiCheckVia === 'pub') {
			$this->isApiPublic = true;
		}

		$this->response = $response;

		$this->helper = $helper;

		$this->config = $config;

		$this->basepackages = $basepackages;

		$this->dispatcher = $dispatcher;

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
				'Home'
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

		$this->registerDefaults(true);
	}

	protected function registerHome()
	{
		if (!$this->isApi) {
			$this->setDefaultNamespace(true);

			$this->router->add(
				'/',
				[
					'controller'	=> 'home',
					'action'		=> 'view'
				]
			);

			$this->router->add(
				'/' . strtolower($this->appInfo['route']),
				[
					'controller'	=> 'home',
					'action'		=> 'view'
				]
			);

			$this->router->add(
				'/' . strtolower($this->appInfo['route']) . '/',
				[
					'controller'	=> 'home',
					'action'		=> 'view'
				]
			);
		}
	}

	protected function registerRoute($givenRoute)
	{
		//Murl/SEO
		if ($this->apps->isMurl) {
			$givenRoute = $this->apps->isMurl['url'];

			$givenRouteArr = explode('/q/', trim($givenRoute, '/'));
			$this->getQuery = $givenRouteArr[1] ?? null;

			if (!$this->domainApiExclusive && !str_starts_with($givenRouteArr[0], $this->appInfo['route'])) {
				$givenRouteArr[0] = $this->appInfo['route'] . '/' . $givenRouteArr[0];
			}
		} else {
			$givenRouteArr[0] = $givenRoute;
		}

		$routeArray = explode('/', $givenRouteArr[0]);

		$this->getGivenRouteClass($routeArray);

		if ($this->getQuery) {
			$routeToMatch = '/' . $givenRouteArr[0] . '/q/' . ':params';
		} else {
			$routeToMatch = '/' . $givenRouteArr[0];
		}

		if ($this->apps->isMurl) {
			$routeToMatch = '/' . ':params';
		}

		if ($this->isApi) {//Assign params to dispatcher manually so that BaseComponent can pic them up
			$params = explode('/', trim($givenRouteArr[1], '/'));

			$this->dispatcher->setParameters($params);
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
		if ($this->domainApiExclusive) {
			unset($routeArray[0]); //Remove app name
		}

		if ($this->request->isGet()) {
			$this->controller = $this->helper->last($routeArray);
			$this->action = 'view';
			unset($routeArray[$this->helper->lastKey($routeArray)]);
			foreach ($routeArray as $route) {
				$this->givenRouteClass .= '\\' . ucfirst($route);
			}
		} elseif ($this->request->isPost()) {
			$this->action = $this->helper->last($routeArray);
			unset($routeArray[$this->helper->lastKey($routeArray)]);
			$this->controller = $this->helper->last($routeArray);
			unset($routeArray[$this->helper->lastKey($routeArray)]);
			foreach ($routeArray as $route) {
				$this->givenRouteClass .= '\\' . ucfirst($route);
			}
		}
	}

	protected function registerDefaults($found = false)
	{
		if ($found) {
			$this->router->setDefaultNamespace($this->defaultNamespace);
			$this->router->setDefaultController($this->controller ?? 'home');
			$this->router->setDefaultAction($this->action ?? 'view');
		} else {
			if (!$this->isApi) {
				$this->router->setDefaultNamespace(
					'System\Base\Providers\ErrorServiceProvider'
				);

				$this->router->setDefaultController('index');

				$this->router->setDefaultAction('view');
			}
		}
	}

	protected function regitserNotFound()
	{
		if (!$this->isApi) {
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
	}

	protected function validateDomain()
	{
		$this->domain = $this->domains->getDomain();
		if (!$this->domain) {
			$this->logger->log->alert(
				'Domain ' . $this->request->getHttpHost() . ' is not registered with system!'
			);

			if ($this->config->debug) {
				throw new DomainNotRegisteredException('Domain ' . $this->request->getHttpHost() . ' is not registered with system!');
			}

			$this->response->setStatusCode(404);
			$this->response->send();
			exit;
		}

		if (isset($this->domain['exclusive_to_default_app']) &&
			$this->domain['exclusive_to_default_app'] == 1
		) {
			$this->appInfo = $this->apps->getAppById($this->domain['default_app_id']);

			$this->domainAppExclusive = true;
		} else  {
			if (isset($this->domain['exclusive_for_api']) && $this->domain['exclusive_for_api'] == 1) {
				$this->domainApiExclusive = true;
			}

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

				if ($this->config->debug) {
					throw new AppNotAllowedException('Trying to access app ' . $this->appInfo['name'] .
						' on domain ' . $this->request->getHttpHost());
				}

				$this->response->setStatusCode(404);
				$this->response->send();
				exit;
			}
		}

		$this->appDefaults['id'] = $this->appInfo['id'];
		$this->appDefaults['app'] = $this->appInfo['route'];
		$this->appDefaults['app_type'] = $this->appInfo['app_type'];
		if (!$this->isApi) {
			$this->appDefaults['component'] =
				$this->components->getComponentById($this->appInfo['default_component'])['route'];
			$this->appDefaults['errorComponent'] =
				isset($this->appInfo['errors_component']) && $this->appInfo['errors_component'] != 0 ?
				$this->components->getComponentById($this->appInfo['errors_component'])['route'] :
				null;
			$this->appDefaults['view'] =
				$this->views->getViewById($this->domain['apps'][$this->appInfo['id']]['view'])['name'];
		}

		return true;
	}

	protected function getURI()
	{
		if (!$this->uri) {
			$uri = explode('/q/', trim($this->requestUri, '/'));

			if ($this->isApi) {
				$uri[0] = explode('/', $uri[0]);
				if ($uri[0][0] === 'api') {
					unset($uri[0][0]);
				}

				$uri[0] = array_values($uri[0]);

				if ($this->isApiPublic) {
					if (isset($uri[0][0]) &&
						$uri[0][0] === 'pub'
					) {
						unset($uri[0][0]);
					}
				}
				$uri[0] = array_values($uri[0]);

				$uri[0] = implode('/', $uri[0]);
			}

			$this->uri = $uri[0];

			$this->getQuery = $uri[1] ?? null;
		}

		return $this->uri;
	}
}