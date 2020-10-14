<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use System\Base\Providers\CookieServiceProvider\CookieJar;

class CookieServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        CookieJar::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->share(CookieJar::class, function () use ($container) {
            return new CookieJar();
        });
    }
}
