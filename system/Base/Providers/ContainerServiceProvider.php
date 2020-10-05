<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use System\Base\Providers\ContainerServiceProvider\Container;

class ContainerServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        Container::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->share(Container::class, function () use ($container) {
            return new Container($container);
        });
    }
}
