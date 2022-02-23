<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DomainsServiceProvider\Domains;

class DomainsServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'domains',
            function () {
                return (new Domains())->init();
            }
        );
    }
}