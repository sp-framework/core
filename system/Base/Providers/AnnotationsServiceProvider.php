<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AnnotationsServiceProvider\Annotations;

class AnnotationsServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'annotations',
            function () {
                return (new Annotations())->init();
            }
        );
    }
}