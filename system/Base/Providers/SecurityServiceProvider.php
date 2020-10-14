<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use System\Base\Providers\SecurityServiceProvider\Csrf;
use System\Base\Providers\SessionServiceProvider\SessionStore;

class SecurityServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        Csrf::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->share(Csrf::class, function () use ($container) {
            return new Csrf(
                $container->get(SessionStore::class)
            );
        });
    }
}