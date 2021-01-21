<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AccessServiceProvider\Acl;
use System\Base\Providers\AccessServiceProvider\Auth;
use System\Base\Providers\AccessServiceProvider\Roles;
use System\Base\Providers\AccessServiceProvider\Accounts;

class AccessServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'accounts',
            function () {
                return (new Accounts())->init();
            }
        );

        $container->setShared(
            'roles',
            function () {
                return (new Roles())->init();
            }
        );

        $container->setShared(
            'auth',
            function () use ($container) {
                $config = $container->getShared('config');
                $session = $container->getShared('session');
                $sessionTools = $container->getShared('sessionTools');
                $cookies = $container->getShared('cookies');
                $accounts = $container->getShared('accounts');
                $applications = $container->getShared('modules')->applications;
                $secTools = $container->getShared('secTools');
                $validation = $container->getShared('validation');
                $logger = $container->getShared('logger');
                $links = $container->getShared('links');

                return (
                    new Auth(
                        $config,
                        $session,
                        $sessionTools,
                        $cookies,
                        $accounts,
                        $applications,
                        $secTools,
                        $validation,
                        $logger,
                        $links
                    ))->init();
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