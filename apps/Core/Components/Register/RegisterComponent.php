<?php

namespace Apps\Core\Components\Register;

use System\Base\BaseComponent;

class RegisterComponent extends BaseComponent
{
    protected $accounts;

    public function initialize()
    {
        $this->accounts = $this->basepackages->accounts;
    }

    public function viewAction()
    {
        //register/q/response_type/code/client_id/YOURCLIENTID/redirect_uri/api_redirect_uri/scope/api_scope_name/state/state_set_by_user(optional)
        if (isset($this->getData()['response_type']) &&
            $this->getData()['response_type'] === 'code' &&
            isset($this->getData()['client_id']) &&
            isset($this->getData()['redirect_uri']) &&
            isset($this->getData()['scope'])
        ) {
            $this->view->clientIdError = false;
            $this->view->scopeError = false;
            $this->view->redirectUrlError = false;

            try {
                $uri = $this->request->getUri();
                preg_match('/__.*/', $uri, $redirectUrl);

                if (!isset($redirectUrl) ||
                    (isset($redirectUrl) && is_array($redirectUrl) && count($redirectUrl) === 1)
                ) {
                    // $redirectUrl = $redirectUrl[0];

                    // $url = explode($redirectUrl, $uri);
                    // $urlParamsArr = explode('/q/', trim($url[0], '/'));

                    // $stringArr = $this->helper->chunk(explode('/', trim($urlParamsArr[1], '/')), 2);

                    // foreach ($stringArr as $value) {
                    //     if (isset($value[1])) {
                    //         $getQueryArr[$value[0]] = $value[1];
                    //     } else {
                    //         $getQueryArr[$value[0]] = 0; //Value not set, so default to 0
                    //     }
                    // }
                    // var_dump($getQueryArr);die();

                    $redirectUrl = str_replace('__', '', $redirectUrl[0]);
                }

                $testRedirectUrl = $this->remoteWebContent->request('GET', $redirectUrl, ['timeout' => 1]);

                if ($testRedirectUrl->getStatusCode() !== 200) {
                    $this->view->redirectUrlError = 'Error: ' . $testRedirectUrl->getStatusCode();
                }
            } catch (\Exception $e) {
                $this->view->redirectUrlError = 'Error: ' . $e->getMessage();
            }
            var_dump($this->getData(), $this->view->redirectUrlError, $redirectUrl);die();

            $uri = explode('/q/', $uri);

            var_dump($uri, $this->getData());die();
        }

        if (isset($this->getData()['api'])) {
            $api = $this->api->getById($this->getData()['api']);

            if (!$api ||
                ($api && ($api['status'] == false || $api && $api['client_keys_generation_allowed'] == false))
            ) {
                $this->response->setStatusCode(404);

                return $this->response->send();

                exit;
            }

            $this->view->setLayout('auth');

            $this->view->api = $api;

            $this->view->pick('register/view');

            return;
        }

        if ($this->auth->hasUserInSession() || $this->auth->hasRecaller()) {
            return $this->response->redirect('/' . strtolower($this->app['route']));
        }

        if (!$this->app || $this->app['registration_allowed'] == '0' || !$this->app['registration_allowed']) {
            $this->response->setStatusCode(404);

            return $this->response->send();

            exit;
        }

        $this->view->setLayout('auth');

        $domain = $this->domains->getDomain();

        if ($this->request->isAjax()) {
            $this->view->disable();
        }
    }

    public function registerNewAccountAction()
    {
        $this->requestIsPost();

        if ($this->accounts->registerAccount($this->postData())) {
            $this->view->redirectUrl = $this->accounts->packagesData->redirectUrl;
        }

        $this->addResponse(
            $this->accounts->packagesData->responseMessage,
            $this->accounts->packagesData->responseCode
        );
    }

    public function apiAddNewClientAction()
    {
        $this->requestIsPost();

        $this->api->clients->addClient($this->postData(), true);

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }

    public function apiClientAction()
    {
        return $this->api->registerClient();
    }
}