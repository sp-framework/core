<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Mvc\Micro\Collection;

class MicroCollection
{
    protected $request;

    protected $application;

    protected $api;

    protected $router;

    protected $domains;

    protected $microCollection;

    function __construct($request, $application, $api, $router, $domains)
    {
        $this->request = $request;

        $this->application = $application;

        $this->api = $api;

        $this->router = $router;

        $this->domains = $domains;

        $this->microCollection = new Collection;
    }

    public function init()
    {
        // trace([$this->router->getRoutes()[0]->getPattern()]);
        if ($this->router->getRoutes() && count($this->router->getRoutes()) > 0) {
            $routeToMatch = $this->router->getRoutes()[0]->getPattern();
        }

        if (isset($this->api->isApiCheckVia) &&
            $this->api->isApiCheckVia === 'pub'
        ) {
            $routeToMatch = '/pub' . $routeToMatch;
        }

        if (isset($this->domains->domain['exclusive_for_api']) &&
            $this->domains->domain['exclusive_for_api'] == false
        ) {
            $routeToMatch = '/api' . $routeToMatch;
        }

        $handler = $this->router->getRoutes()[0]->getPaths()['namespace'] . '\\Api';
        $action = $this->router->getRoutes()[0]->getPaths()['action'];
        $controller = $this->router->getRoutes()[0]->getPaths()['controller'];

        if ($action === 'view') {//Make sure methods are all Caps, else route will not match!
            $methods = ['GET'];
        } else if ($action === 'add' || $action === 'update') {
            $methods = ['POST','PATCH','PUT'];
        } else if ($action === 'remove') {
            $methods = ['DELETE'];
        } else {
            $methods = ['POST'];
        }

        $handlerMethod = lcfirst($action) . 'Action';

        $this->microCollection->setHandler($handler, true);
        $this->microCollection->mapVia($routeToMatch, $handlerMethod, $methods, $controller);

        $this->regitserNotFound();

        return $this;
    }

    public function getMicroCollection()
    {
        return $this->microCollection;
    }

    protected function regitserNotFound()
    {
        $application = $this->application;

        $this->application->notFound(
            function () use ($application) {
                $application->response
                            ->setStatusCode(404, 'API Route Not Found')
                            ->sendHeaders()
                            ->setContent('API Route Not Found')
                            ->send();
            }
        );
    }
}