<?php

namespace System\Base\Providers\RouterServiceProvider;

use Phalcon\Mvc\Micro\Collection;

class MicroCollection
{
    protected $application;

    protected $api;

    protected $router;

    protected $microCollection;

    function __construct($application, $api, $router)
    {
        $this->application = $application;

        $this->api = $api;

        $this->router = $router;

        $this->microCollection = new Collection;
    }

    public function init()
    {
        if ($this->router->getRoutes() && count($this->router->getRoutes()) > 0) {
            $routeToMatch = $this->router->getRoutes()[0]->getPattern();
        }

        $routeToMatch = '/api' . $routeToMatch;

        $handler =
            $this->router->getRoutes()[0]->getPaths()['namespace'] .
            '\\' .
            ucfirst($this->router->getRoutes()[0]->getPaths()['controller']) . 'Component';

        if ($this->router->getRoutes()[0]->getPaths()['action'] === 'view') {//Make sure methods are all Caps, else route will not match!
            $methods = ['GET'];
            $handlerMethod = 'ViewAction';
        } else {
            $methods = ['POST'];
            $handlerMethod = ucfirst($this->router->getRoutes()[0]->getPaths()['action']) . 'Action';
        }

        $this->microCollection->setHandler($handler, true);

        $this->microCollection->mapVia($routeToMatch, $handlerMethod, $methods, $this->router->getRoutes()[0]->getPaths()['controller']);

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
                            ->setStatusCode(404, 'Not Found')
                            ->sendHeaders()
                            ->setContent('Not Found')
                            ->send();
            }
        );
    }
}