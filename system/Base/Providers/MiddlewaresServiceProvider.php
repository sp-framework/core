<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;

class MiddlewaresServiceProvider extends Injectable
{
    public function beforeExecuteRoute(
        Event $event,
        DispatcherInterface $dispatcher,
        $data
    ) {
        $application = $this->modules->applications->getApplicationInfo();

        $middlewares =
            msort($this->modules->middlewares->getMiddlewaresForApplication($application['id']), 'sequence');

        foreach ($middlewares as $middleware) {
            if ($middleware['enabled'] === '1') {
                $middlewareClass = $middleware['class'] . '\\' . $middleware['name'];
                (new $middlewareClass())->process();
            }
        }
    }
}