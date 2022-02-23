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
        $app = $this->apps->getAppInfo();

        if ($app) {
            $middlewares =
                msort($this->modules->middlewares->getMiddlewaresForApp($app['id']), 'sequence');

            foreach ($middlewares as $middleware) {
                if ($middleware['enabled'] == true) {
                    try {
                        $mw = (new $middleware['class']())->process();
                    } catch (\Exception $e) {
                        if ($this->config->logs->exceptions) {
                            $this->logger->logExceptions->debug($e);
                        }

                        throw $e;
                    }

                    //If there is a redirect or null returned from process
                    if ($mw && $mw instanceof \Phalcon\Http\Response) {
                        if ($mw->getHeaders()->toArray()['Status'] === '302 Found') {
                            $notFound = true;
                            break;
                        }
                    }

                    if ($mw === false) {
                        break;
                    }
                }
            }

            if (isset($notFound) && $notFound) {
                return false;
            }

            return true;
        }
    }
}