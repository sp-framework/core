<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AccessServiceProvider\Acl;
use System\Base\Providers\AccessServiceProvider\Users;

class AccessServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'users',
            function () {
                return (new Users())->init();
            }
        );

        $container->setShared(
            'acl',
            function () {
                return (new Acl())->init();
            }
        );
    }
}