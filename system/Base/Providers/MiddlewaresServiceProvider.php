<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;

class MiddlewaresServiceProvider extends Injectable
{
    protected $data = [];

    protected function init($data)
    {
        if ($data) {
            $this->data = array_merge($this->data, $data);
        }

        $this->data['app'] = $this->apps->getAppInfo();
    }

    public function beforeExecuteRoute(
        Event $event,
        DispatcherInterface $dispatcher,
        $data
    ) {
        $this->init($data);

        if ($this->data['app']) {
            $middlewares = [];

            $ipFilterMiddleware = $this->modules->middlewares->getMiddlewareByNameForAppId('IpFilter', $this->data['app']['id']);

            if ($ipFilterMiddleware) {
                $middlewares[] = $ipFilterMiddleware;
                $middlewares = array_merge($middlewares, msort($this->modules->middlewares->getMiddlewaresForAppId($this->data['app']['id']), 'sequence'));
            } else {
                $middlewares = msort($this->modules->middlewares->getMiddlewaresForAppId($this->data['app']['id']), 'sequence');
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

    public function afterExecuteRoute(
        Event $event,
        DispatcherInterface $dispatcher,
        $data
    ) {
        if (!$this->dispatcher->wasForwarded()) {
            return;
        }

        $this->init($data);

        if ($this->data['app']) {
            $middlewares = $this->modules->middlewares->getMiddlewaresForAppId($this->data['app']['id']);

            foreach ($middlewares as $middleware) {
                if ($middleware['name'] !== 'Acl' &&
                    $middleware['enabled'] == true
                ) {
                    if ($this->checkRoute($middleware)) {
                        return true;
                    }

                    try {
                        $mw = (new $middleware['class']())->process($this->data);
                    } catch (\Exception $e) {
                        if ($this->config->logs->exceptions) {
                            $this->logger->logExceptions->debug($e);
                        }

                        throw $e;
                    }
                }
            }
        }
    }

    protected function checkRoute($middleware)
    {
        $this->data['domain'] = $this->domains->getDomain();

        if ($this->apps->isMurl) {
            $this->data['givenRoute'] = strtolower(rtrim(explode('/q/', $this->apps->isMurl['url'])[0], '/'));
        } else {
            $this->data['givenRoute'] = strtolower(rtrim(explode('/q/', $this->request->getUri())[0], '/'));
        }

        if (isset($this->data['domain']['exclusive_to_default_app']) &&
            $this->data['domain']['exclusive_to_default_app'] == 1
        ) {
            $this->data['appRoute'] = '';
        } else {
            if ($this->apps->isMurl) {
                $givenRoute = explode('/', trim($this->data['givenRoute'], '/'));

                if ($givenRoute[0] !== strtolower($this->data['app']['route'])) {
                    array_unshift($givenRoute, strtolower($this->data['app']['route']));
                }
                $this->data['givenRoute'] = '/' . implode('/', $givenRoute);
            }

            $this->data['appRoute'] = '/' . strtolower($this->data['app']['route']);
        }

        if ($this->data['givenRoute'] === $this->data['appRoute']) {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }
        if ($this->data['givenRoute'] === '') {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }


        if ($this->request->isGet()) {
            $this->data['guestAccess'] =
            [
                $this->data['appRoute'] . '/auth',
                $this->data['appRoute'] . '/register',
            ];
        } else if ($this->request->isPost()) {
            $this->data['guestAccess'] =
            [
                $this->data['appRoute'] . '/auth/login',
                $this->data['appRoute'] . '/auth/forgot',
                $this->data['appRoute'] . '/auth/pwreset',
                $this->data['appRoute'] . '/auth/checkpwstrength',
                $this->data['appRoute'] . '/auth/generatepw',
                $this->data['appRoute'] . '/auth/enabletwofatotp',
                $this->data['appRoute'] . '/auth/verifytwofatotp',
                $this->data['appRoute'] . '/auth/logout',
                $this->data['appRoute'] . '/auth/sendverification',
                $this->data['appRoute'] . '/auth/verify',
                $this->data['appRoute'] . '/auth/sendtwofaemail',
                $this->data['appRoute'] . '/register/registernewaccount',
                $this->data['appRoute'] . '/register/apiaddnewclient',
                $this->data['appRoute'] . '/register/apiclient',
            ];
        }

        if (in_array($this->data['givenRoute'], $this->data['guestAccess'])) {
            return true;
        } else if ($middleware['name'] === 'Auth' && !$this->componentsNeedsAuth()) {
            return true;
        }

        return false;
    }

    protected function componentsNeedsAuth()
    {
        $componentsArr = $this->modules->components->getComponentsForAppType($this->data['app']['app_type']);

        foreach ($componentsArr as $key => $componentValue) {
            $match = false;

            $methods = (new \ReflectionClass($componentValue['class']))->getMethods();

            foreach ($methods as $key => $method) {
                if ((strtolower($method->class) === strtolower($componentValue['class']) && str_contains($method->name, 'Action')) ||
                    $method->name === 'msviewAction' ||
                    $method->name === 'msupdateAction'
                ) {
                    if (strtolower($this->data['givenRoute']) ===
                        strtolower(
                            $this->data['appRoute'] . '/' . $componentValue['route'] . '/' . str_replace('Action', '', $method->name)
                        )
                    ) {
                        $match = true;
                    }
                }
            }

            if (strtolower($this->data['givenRoute']) === strtolower($this->data['appRoute'] . '/' . $componentValue['route']) ||
                $match === true
            ) {
                if ($componentValue['apps']) {
                    $componentValue['apps'] = $this->helper->decode($componentValue['apps'], true);

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