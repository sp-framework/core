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

        if ($application) {
            $middlewares =
                msort($this->modules->middlewares->getMiddlewaresForApplication($application['id']), 'sequence');

            foreach ($middlewares as $middleware) {
                if ($middleware['enabled'] == true) {
                    $middlewareClass = $middleware['class'] . '\\' . $middleware['name'];
                    $mw = (new $middlewareClass())->process();

                    //If there is a redirect or null returned from process
                    if ($mw && $mw instanceof \Phalcon\Http\Response) {
                        if ($mw->getHeaders()->toArray()['Status'] === '302 Found') {
                            break;
                        }
                    }

                    if ($mw === false) {
                        break;
                    }
                }
            }
        }
    }
}