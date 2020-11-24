<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\BasepackagesServiceProvider\Basepackages;

class BasepackagesServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'basepackages',
            function () {
                return new Basepackages();
            }
        );
    }
}