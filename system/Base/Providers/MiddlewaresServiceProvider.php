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
        $middlewares =
            msort($this->modules->middlewares->middlewares, 'sequence');

        foreach ($middlewares as $middleware) {
            $middlewareClass = $middleware['class'] . '\\' . $middleware['name'];
            if ($middleware['enabled'] === '1') {
                (new $middlewareClass())->process();
            }
        }
    }
}