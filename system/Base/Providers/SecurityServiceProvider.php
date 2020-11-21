<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\SecurityServiceProvider\Crypt;
use System\Base\Providers\SecurityServiceProvider\Random;
use System\Base\Providers\SecurityServiceProvider\SecTools;
use System\Base\Providers\SecurityServiceProvider\Security;

class SecurityServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'security',
            function () {
                return (new Security())->init();
            }
        );

        $container->setShared(
            'random',
            function () {
                return (new Random())->init();
            }
        );

        $container->setShared(
            'crypt',
            function () {
                return (new Crypt())->init();
            }
        );


        $container->setShared(
            'secTools',
            function () use ($container) {
                $security = $container->getShared('security');
                $random = $container->getShared('random');
                $crypt = $container->getShared('crypt');
                return (new SecTools($security, $random, $crypt))->init();
            }
        );
    }
}