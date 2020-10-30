<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ValidationServiceProvider\Validation;

class ValidationServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'validation',
            function () {
                return (new Validation())->init();
            }
        );
    }
}