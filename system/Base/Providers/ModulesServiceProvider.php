<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ModulesServiceProvider\Core;
use System\Base\Providers\ModulesServiceProvider\Applications;
use System\Base\Providers\ModulesServiceProvider\Components;
use System\Base\Providers\ModulesServiceProvider\Packages;
use System\Base\Providers\ModulesServiceProvider\Views;

class ModulesServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        // $container->setShared('repositories', function () use ($container) {
        //     return new Repositories($container);
        // });

        $container->setShared(
            'core',
            function () use ($container) {
                return new Core($container);
            }
        );

        $container->setShared(
            'applications',
            function () use ($container) {
                return new Applications($container);
            }
        );

        $container->setShared(
            'components',
            function () use ($container) {
                return new Components($container);
            }
        );

        $container->setShared(
            'packages',
            function () use ($container) {
                return new Packages($container);
            }
        );

        // $container->setShared(
        // 'middlewares',
        // function () use ($container) {
            //     return new Middlewares($container);
            //
             // });

        $container->setShared(
            'views',
            function () use ($container) {
                return new Views($container);
            }
        );

        $container->setShared(
            'view',
            function () use ($container) {
                return $container->getShared('views')->registerPhalconView();
            }
        );

        //     $config = $container->get('config');

        //     $db = $this->container->get('db');

        //     $em = $this->container->get('em');

        //     $applicationInfo = $this->container->get('applications')->getApplicationInfo();
        //     if ($applicationInfo) {
        //         $applicationDefaults = $this->container->get('applications')->getApplicationDefaults($applicationInfo['name']);
        //     } else {
        //         $applicationDefaults = null;
        //     }

        //     if ($applicationInfo && $applicationDefaults) {

        //         $applicationName = $applicationDefaults['application'];

        //         $viewsName = $applicationDefaults['view'];

        //         $cache = json_decode(
        //                     $em->getRepository(ViewsModel::class)
        //                         ->findBy(['name' => $viewsName, 'application_id' => $applicationDefaults['id']])[0]
        //                         ->getAllArr()['settings'],
        //                     true
        //                 )['cache'];

        //         //Enable debugging for twig. Per application debug allowed, if nothing found then base.debug is used.
        //         if ($applicationInfo['mode'] === 0) {
        //             $applicationDebug = false;
        //         } else if ($applicationInfo['mode'] === 1) {
        //             $applicationDebug = true;
        //         } else {
        //             $applicationDebug = $config->get('base.debug');
        //         }

        //         $twigSettings = [
        //             'debug' => $applicationDebug
        //         ];


        //         if ($cache === 'true' || $cache === true) {
        //             $twigSettings =
        //                 array_merge(
        //                     $twigSettings,
        //                     ['cache' => 'views/' . $applicationName . '/' . $viewsName . '/html_compiled/']
        //                 );
        //         }
        //     } else {
        //         $applicationName = 'Admin';
        //         $viewsName = 'Default';
        //         $cache = false;
        //         $twigSettings = [
        //             'debug' => $config->get('base.debug')
        //         ];
        //         // $loader = new FilesystemLoader(base_path('system/Base/Installer/Views/'));
        //     }
        //     $loader = new FilesystemLoader(base_path('views/'));

        //     $twig = new Environment($loader, $twigSettings);

        //     if ($config->get('base.debug') || $applicationDebug) {
        //         $twig->addExtension(new DebugExtension);
        //     }

        //     $views = new Views($twig, $container->get('request'), $db, $em, $viewsName, $applicationName);

        //     return $views;
        // });

        // Share With Views
        // $container->get('views')->share([
        //     'flash'     => $container->get(Flash::class)
        // ]);
        //
    }
}
