<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Helper\Json;
use Phalcon\Mvc\DispatcherInterface;

class MiddlewaresServiceProvider extends Injectable
{
    protected $data = [];

    public function beforeExecuteRoute(
        Event $event,
        DispatcherInterface $dispatcher,
        $data
    ) {
        $this->data['app'] = $this->apps->getAppInfo();

        if ($this->data['app']) {
            $middlewares = [];

            $ipFilterMiddleware = $this->modules->middlewares->get(['name' => 'IpFilter', 'app_id' => $this->data['app']['id']]);

            if ($ipFilterMiddleware) {
                $middlewares[] = $ipFilterMiddleware;

                $middlewares = array_merge($middlewares, msort($this->modules->middlewares->get(['app_id' => $this->data['app']['id']]), 'sequence'));
            } else {
                $middlewares = msort($this->modules->middlewares->get(['app_id' => $this->data['app']['id']]), 'sequence');
            }

            foreach ($middlewares as $middleware) {
                if ($middleware['name'] !== 'IpFilter') {
                    if ($this->checkRoute($middleware)) {
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

    protected function checkRoute($middleware)
    {
        $this->data['domain'] = $this->domains->getDomain();

        if (isset($this->data['domain']['exclusive_to_default_app']) &&
            $this->data['domain']['exclusive_to_default_app'] == 1
        ) {
            $this->data['appRoute'] = '';
        } else {
            $this->data['appRoute'] = '/' . strtolower($this->data['app']['route']);
        }

        $this->data['givenRoute'] = strtolower(rtrim(explode('/q/', $this->request->getUri())[0], '/'));

        if ($this->data['givenRoute'] === $this->data['appRoute']) {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }
        if ($this->data['givenRoute'] === '') {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }

        $this->data['guestAccess'] =
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

        if (in_array($this->data['givenRoute'], $this->data['guestAccess'])) {
            return true;
        } else if ($middleware['name'] === 'Auth' && !$this->componentsNeedsAuth()) {
            return true;
        }

        return false;
    }

    protected function componentsNeedsAuth()
    {
        $componentsArr = $this->modules->components->get(['app_type' => $this->data['app']['app_type']]);

        foreach ($componentsArr as $key => $componentValue) {
            $match = false;

            $methods = (new \ReflectionClass($componentValue['class']))->getMethods();

            foreach ($methods as $key => $method) {
                if ($method->class === $componentValue['class'] && str_contains($method->name, 'Action')) {
                    if ($this->data['givenRoute'] ===
                        $this->data['appRoute'] . '/' . $componentValue['route'] . '/' . str_replace('Action', '', $method->name)
                    ) {
                        $match = true;
                    }
                }
            }

            if ($this->data['givenRoute'] === $this->data['appRoute'] . '/' . $componentValue['route'] || $match === true) {
                if ($componentValue['apps']) {

                    if (!isset($componentValue['apps'][$this->data['app']['id']]['needAuth'])) {
                        return false;
                    } else {
                        if ($componentValue['apps'][$this->data['app']['id']]['needAuth'] == true ||
                            $componentValue['apps'][$this->data['app']['id']]['needAuth'] == 'mandatory'
                        ) {
                            return true;
                        } else if ($componentValue['apps'][$this->data['app']['id']]['needAuth'] == false ||
                                   $componentValue['apps'][$this->data['app']['id']]['needAuth'] == 'disabled'
                        ) {
                            return false;
                        }
                    }
                }
            }
        }

        return false;
    }
}