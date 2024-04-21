<?php

namespace System\Base\Providers;

use GuzzleHttp\Psr7\Response;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;
use System\Base\Providers\AccessServiceProvider\Exceptions\PermissionDeniedException;

class MicroMiddlewaresServiceProvider extends Injectable
{
    protected $apiResponse;

    protected $data = [];

    protected function init()
    {
        $this->data['api'] = $this->api->getApiInfo(true);
        $this->data['app'] = $this->apps->getAppInfo();
        $this->data['domain'] = $this->domains->getDomain();

        $this->setHeader();
    }

    public function beforeExecuteRoute(
        Event $event,
        $micro,
    ) {
        $this->init();

        if (!$this->preCheck()) {
            return false;
        }

        try {
            if (($checkMw = $this->checkAllMiddlewares()) !== true) {
                if ($checkMw === 'auth') {
                    $this->addResponse('Authentication Error!', 1);
                } else if ($checkMw === 'acl') {
                    $this->addResponse('Permission Denied!', 1);
                }

                return false;
            }

            $refreshTokenSet = false;

            if ($this->request->get('refresh_token')) {
                $refreshTokenSet = true;
            }

            $this->api->setupApi($refreshTokenSet);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function afterExecuteRoute(
        Event $event,
        $micro
    ) {
        if ($micro->getReturnedValue() && $micro->getReturnedValue() instanceof Response) {//Response from token generator
            $body = $micro->getReturnedValue()->getBody();

            if ($body) {
                try {
                    $body = $this->helper->decode((string) $body, true);
                } catch (\Exception $e) {
                    //Returned is not Json. Check Exception Logs
                    $this->addResponse('Error! Contact Administrator.', 1);
                }
            }

            if (isset($body['error'])) {
                if (isset($body['error_description'])) {
                    $this->addResponse($body['error_description'], 1);
                } else if (isset($body['message'])) {
                    $this->addResponse($body['message'], 1);
                }

                return false;
            }

            if (is_array($body)) {
                if (!isset($body['expires_in'])) {
                    $this->addResponse('Ok', 0, $body);
                }

                $this->apiResponse = $body;

                $this->sendJson();
            }
        }

        $this->api->clients->setClientsLastUsed($this->api->client);
    }

    protected function checkAllMiddlewares()
    {
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
                if ($middleware['name'] === 'Auth' && $this->data['api']['is_public'] == true) {
                    continue;
                }

                try {
                    $mw = (new $middleware['class']())->process($this->data);
                } catch (\Exception $e) {
                    if ($this->config->logs->exceptions) {
                        $this->logger->logExceptions->debug($e);
                    }

                    if (str_contains(strtolower($e->getMessage()), 'denied') || str_contains(strtolower($e->getMessage()), 'expired')) {
                        return 'auth';
                    }

                    if ($e instanceof PermissionDeniedException) {
                        return 'acl';
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

    protected function checkRoute($middleware)
    {
        if (isset($this->data['domain']['exclusive_for_api']) &&
            $this->data['domain']['exclusive_for_api'] == 1
        ) {
            if ($this->data['api']['is_public'] == true) {
                $this->data['appRoute'] = '/pub';
            } else {
                $this->data['appRoute'] = '';
            }
        } else {
            if ($this->data['api']['is_public'] == true) {
                $this->data['appRoute'] = '/api/pub';
            } else {
                $this->data['appRoute'] = '/api';
            }
        }
        if (isset($this->data['domain']['exclusive_to_default_app']) &&
            $this->data['domain']['exclusive_to_default_app'] != 1
        ) {
            $this->data['appRoute'] = $this->data['appRoute'] . '/' . strtolower($this->data['app']['route']);
        }
        $this->data['appRoute'] = $this->helper->reduceSlashes($this->data['appRoute']);
        $this->data['givenRoute'] = strtolower(rtrim(explode('/q/', $this->request->getUri())[0], '/'));

        if ($this->data['givenRoute'] === $this->data['appRoute']) {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }
        if ($this->data['givenRoute'] === '') {
            $this->data['givenRoute'] = $this->data['appRoute'] . '/home';
        }
        $this->data['guestAccess'] =
        [
            $this->data['appRoute'] . '/register/client',
        ];

        if (in_array($this->data['givenRoute'], $this->data['guestAccess'])) {
            return true;
        } else if ($middleware['name'] === 'Auth' &&
                   $this->data['api']['is_public'] == false &&
                   !$this->componentsNeedsAuth()
        ) {
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
                if ((strtolower($method->class) === strtolower($componentValue['class']) && str_contains($method->name, 'api') && str_contains($method->name, 'Action'))) {
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

    protected function setHeader()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader('Cache-Control', 'no-store');
    }

    protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null)
    {
        $this->apiResponse['responseMessage'] = $responseMessage;
        $this->apiResponse['responseCode'] = $responseCode;

        if ($responseData) {
            $this->apiResponse['responseData'] = $responseData;
        }

        $this->sendJson();
    }

    protected function sendJson()
    {
        $this->setHeader();

        if ($this->response->isSent() !== true) {
            $this->response->setJsonContent($this->apiResponse);

            $this->response->send();
        }
    }

    protected function preCheck()
    {
        if (!$this->data['app']) {
            $this->addResponse('APP not available!', 1);

            return false;
        }

        if (!$this->data['api']) {
            if ($this->api->apiCallsLimitReached) {
                $this->addResponse($this->api->packagesData->responseMessage, $this->api->packagesData->responseCode);

                return false;
            }

            $this->addResponse('API not available or Incorrect client ID or No Authorization Code set.', 1);

            return false;
        }

        $url = $this->request->getURI();

        if (isset($this->data['api']['registration_allowed']) &&
            $this->data['api']['registration_allowed'] == false &&
            str_contains($url, 'register/client') &&
            isset($this->request->getPost()['grant_type']) &&
            $this->request->getPost()['grant_type'] !== 'refresh_token'
        ) {
            $this->addResponse('API registration not allowed on this api!', 1);

            return false;
        }

        return true;
    }
}