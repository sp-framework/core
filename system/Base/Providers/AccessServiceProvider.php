<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AccessServiceProvider\Acl;
use System\Base\Providers\AccessServiceProvider\Auth;
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

        $session = $container->getShared('session');
        $cookies = $container->getShared('cookies');
        $users = $container->getShared('users');
        $applications = $container->getShared('modules')->applications;
        $secTools = $container->getShared('secTools');

        $container->setShared(
            'auth',
            function () use ($session, $cookies, $users, $applications, $secTools) {
                return (new Auth($session, $cookies, $users, $applications, $secTools))->init();
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