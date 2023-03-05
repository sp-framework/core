<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;

class MiddlewaresServiceProvider extends Injectable
{
    protected $data = [];

    public function beforeExecuteRoute(
        Event $event,
        DispatcherInterface $dispatcher,
        $data
    ) {
        $app = $this->apps->getAppInfo();

        if ($app) {
            $middlewares = [];

            $ipFilterMiddleware = $this->modules->middlewares->getNamedMiddlewareForApp('IpFilter', $app['id']);

            if ($ipFilterMiddleware) {
                $middlewares[] = $ipFilterMiddleware;
                $middlewares = array_merge($middlewares, msort($this->modules->middlewares->getMiddlewaresForApp($app['id'], true), 'sequence'));
            } else {
                $middlewares = msort($this->modules->middlewares->getMiddlewaresForApp($app['id'], true), 'sequence');
            }

            foreach ($middlewares as $middleware) {
                if ($middleware['name'] !== 'IpFilter') {
                    if ($this->checkRoute($app)) {
                        return true;
                    };
                }

                if ($middleware['enabled'] == true) {
                    try {
                        $mw = (new $middleware['class']())->process($this->data);
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
                }
            }

            if (isset($notFound) && $notFound) {
                return false;
            }

            return true;
        }
    }

    protected function checkRoute($app)
    {
        $this->data['domain'] = $this->domains->getDomain();

        if (isset($this->data['domain']['exclusive_to_default_app']) &&
            $this->data['domain']['exclusive_to_default_app'] == 1
        ) {
            $this->data['appRoute'] = '';
        } else {
            $this->data['appRoute'] = '/' . strtolower($app['route']);
        }

        $givenRoute = strtolower(rtrim(explode('/q/', $this->request->getUri())[0], '/'));

        $guestAccess =
        [
            $this->data['appRoute'] . '/auth',
            $this->data['appRoute'] . '/auth/login',
            $this->data['appRoute'] . '/auth/forgot',
            $this->data['appRoute'] . '/auth/pwreset',
            $this->data['appRoute'] . '/auth/logout',
            $this->data['appRoute'] . '/auth/sendverification',
            $this->data['appRoute'] . '/register',
            $this->data['appRoute'] . '/register/registernewaccount'
        ];

        if (in_array($givenRoute, $guestAccess)) {
            return true;
        }
    }
}