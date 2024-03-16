<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\AccessServiceProvider\Acl;
use System\Base\Providers\AccessServiceProvider\Auth;

class AccessServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'auth',
            function () use ($container) {
                $request = $container->getShared('request');
                $config = $container->getShared('config');
                $session = $container->getShared('session');
                $sessionTools = $container->getShared('sessionTools');
                $cookies = $container->getShared('cookies');
                $apps = $container->getShared('apps');
                $secTools = $container->getShared('secTools');
                $validation = $container->getShared('validation');
                $logger = $container->getShared('logger');
                $links = $container->getShared('links');
                $accounts = $container->getShared('basepackages')->accounts;
                $profile = $container->getShared('basepackages')->profile;
                $roles = $container->getShared('basepackages')->roles;
                $email = $container->getShared('basepackages')->email;
                $emailQueue = $container->getShared('basepackages')->emailqueue;
                $domains = $container->getShared('domains');
                $ff = $container->getShared('ff');
                $core = $container->getShared('core');
                $basepackages = $container->getShared('basepackages');
                $helper = $container->getShared('helper');

                return (
                    new Auth(
                        $request,
                        $config,
                        $session,
                        $sessionTools,
                        $cookies,
                        $apps,
                        $secTools,
                        $validation,
                        $logger,
                        $links,
                        $accounts,
                        $profile,
                        $roles,
                        $email,
                        $emailQueue,
                        $domains,
                        $ff,
                        $core,
                        $basepackages,
                        $helper
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