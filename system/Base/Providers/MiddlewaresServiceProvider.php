<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Router;
use System\Base\Providers\ModulesServiceProvider\Applications;
use System\Base\Providers\ModulesServiceProvider\Model\Middlewares;

class MiddlewaresServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [];

    public function register()
    {
        //
    }

    public function boot()
    {
        $container = $this->getContainer();

        $route = $container->get(Router::class);

        $db = $container->get('db');

        $applications = new Applications($container);

        if ($applications->getApplicationInfo()) {
            $applicationId = $applications->getApplicationInfo()['id'];
        } else {
            $applicationId = null;
        }

        $middlewares =
            $db->getByData(Middlewares::class, ['application_id' => $applicationId], ['sequence' => 'ASC']);

        foreach ($middlewares as $middlewareKey => $middleware) {
            if ($middleware->get('enabled') == 1) {
                $route->middleware($container->get($middleware->get('class')));
            }
        }
    }
}