<?php

namespace System\Base\Providers;

use Laminas\Diactoros\ResponseFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use System\Base\Providers\SessionServiceProvider\Flash;

class RouteServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $router;

    protected $container;

    protected $givenRouteClass = '';

    private $loopCount;

    protected $serverParams;

    protected $requestMethod;

    protected $requestUri;

    protected $queryString;

    protected $auth;

    protected $strategy;

    protected $applicationInfo;

    protected $applicationDefaults;

    protected $defaultComponent;

    // protected $provides = ['rsp'];

    public function register()
    {
        // $this->container = $this->getContainer();

        // $this->container->share('rsp', function() {
        //     return $this;
        // });
    }

    public function boot()
    {
        $this->container = $this->getContainer();

        $this->router = $this->container->get(Router::class);

        $this->serverParams = $this->container->get('request')->getServerParams();

        $this->requestMethod = $this->serverParams['REQUEST_METHOD'];

        $this->requestUri = $this->serverParams['REQUEST_URI'];

        $this->queryString = $this->serverParams['QUERY_STRING'];

        $this->applicationInfo = $this->container->get('applications')->getApplicationInfo();

        if ($this->applicationInfo) {

            $this->applicationDefaults = $this->container->get('applications')->getApplicationDefaults();

            if (!$this->applicationDefaults ||
                $this->applicationInfo['name'] !== $this->applicationDefaults['application']
               ) {
                $this->applicationDefaults =
                    $this->container->get('applications')->getApplicationDefaults($this->applicationInfo['name']);
            }
        }

        if (strpos($this->serverParams['HTTP_ACCEPT'], 'application/json') === 0) {
            $this->strategy = (new JsonStrategy(new ResponseFactory))->setContainer($this->container);
        } else {
            $this->strategy = (new ApplicationStrategy)->setContainer($this->container);
        }

        $uri = explode('?', trim($this->requestUri, '/'));

        if ($this->applicationInfo && $this->applicationDefaults) {
            if ($this->applicationInfo['name'] !== $this->applicationDefaults['application']) {
                if ($uri[0] !== '' &&
                    $uri[0] !== strtolower($this->applicationInfo['name']) &&
                    $uri[0] !== strtolower($this->applicationInfo['route'])
                    ) {
                    $this->registerRoute($uri[0], 'Components');
                // } else if ($uri[0] === '' ||
                //            $uri[0] === strtolower($this->applicationInfo['name']) ||
                //            $uri[0] === strtolower($this->applicationInfo['route'])
                //     ) {
                //     $this->registerHome();
                }
            } else {
                if ($uri[0] !== '' &&
                    $uri[0] !== strtolower($this->applicationDefaults['application']) &&
                    $uri[0] !== strtolower($this->applicationInfo['route'])
                    ) {

                    $this->registerRoute($uri[0], 'Components');

                } else if ($uri[0] === '' ||
                           $uri[0] === strtolower($this->applicationDefaults['application']) ||
                           $uri[0] === strtolower($this->applicationInfo['route'])
                    ) {
                    $this->registerHome($uri[0]);
                }
            }
        // } else {
        //     if (explode('/', $uri[0])[0] === 'base') {
        //         $this->registerRoute($uri[0], 'System\Base\Installer\Components');
        //     } else {
        //         //
        //     }
        }
    }

    protected function registerHome($uri)
    {
        if ($this->requestMethod === 'GET') {
            if ($uri !== strtolower($this->applicationInfo['route'])) {
                $this->router->get(
                    '/',
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);

                $this->router->get(
                    '/' . strtolower($this->applicationDefaults['application']),
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);

                $this->router->get(
                    '/' . strtolower($this->applicationDefaults['application']) . '/',
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);
            } else if (($uri === strtolower($this->applicationInfo['route'])) &&
                        strtolower($this->applicationInfo['route']) !== ''
                      ) { //we access via configured route
                $this->router->get(
                    '/' . strtolower($this->applicationInfo['route']),
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);

                $this->router->get(
                    '/' . strtolower($this->applicationInfo['route']) . '/',
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);
                // var_dump($uri, $this->applicationInfo['route']);
            } else {
                $this->router->get(
                    '/',
                    'Components\\' . ucfirst($this->applicationDefaults['application']) . '\\' . $this->applicationDefaults['component'] . '::view'
                )->setStrategy($this->strategy);
            }
        }
    }

    public function registerRoute($givenRoute, $class)
    {
        $routeArray = explode('/', $givenRoute);
        // var_dump($routeArray[0], strtolower($this->applicationInfo['name']));
        if ($routeArray[0] !== 'base' &&
            $routeArray[0] !== strtolower($this->applicationInfo['name'])
           ) {
            $routeArray[0] = $this->applicationInfo['name'];
        }

        $this->getGivenRouteClass($routeArray, $this->requestMethod);

            // var_dump($givenRoute,
            //     $class . $this->givenRouteClass . '::view');
        if ($this->requestMethod === 'GET') {
            $this->router->get(
                $givenRoute,
                $class . $this->givenRouteClass . '::view'
            )->setStrategy($this->strategy);
        } elseif ($this->requestMethod === 'POST') {
            $this->router->post(
                $givenRoute,
                $class . $this->givenRouteClass . '::' . $routeArray[$this->loopCount]
            )->setStrategy($this->strategy);
        }
    }

    protected function getGivenRouteClass(array $routeArray, $method = 'GET')
    {
        if ($method === 'GET') {
            $this->loopCount = count($routeArray);
        } elseif ($method === 'POST') {
            $this->loopCount = count($routeArray) - 1;
        }

        for ($i = 0; $i < $this->loopCount; $i++) {
            if ($i === 0) {
                $this->givenRouteClass .= '\\' . ucfirst($routeArray[$i]);
            } else {
                $this->givenRouteClass .= '\\' . ucfirst($routeArray[$i]);
            }
        }
    }
}