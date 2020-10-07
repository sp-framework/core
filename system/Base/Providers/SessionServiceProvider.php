<?php

namespace System\Base\Providers;

use Phalcon\Di\ServiceProviderInterface;

class SessionServiceProvider extends ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
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