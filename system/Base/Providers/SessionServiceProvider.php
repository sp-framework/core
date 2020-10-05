<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use System\Base\Providers\SessionServiceProvider\Flash;
use System\Base\Providers\SessionServiceProvider\Session;
use System\Base\Providers\SessionServiceProvider\SessionStore;

class SessionServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        SessionStore::class,
        Flash::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->share(SessionStore::class, function () {
            return new Session();
        });

        $container->share(Flash::class, function () use ($container) {
            return new Flash(
                $container->get(SessionStore::class)
            );
        });
    }
}
