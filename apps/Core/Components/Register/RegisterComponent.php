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

    /**
     * View action for user account or API registration for the user.
     *
     * - Users can register for a regular user account if the app permits. The App settings allow registration should be enabled.
     * - For API registration: API services should allow registration else they will get 404
     *
     */
    public function viewAction()
    {
        if (isset($this->getData()['response_type']) &&
            $this->getData()['response_type'] === 'code' &&
            isset($this->getData()['client_id']) &&
            isset($this->getData()['redirect_uri']) &&
            isset($this->getData()['scope'])
        ) {//Authorize Flow 1
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $api = $this->api->checkAuthorizationLinkData($this->getData());

            if (!$api) {
                $this->view->error = $this->api->packagesData->responseMessage;

                return;
            }

            $this->view->authorizationTosPp = null;
            if (isset($api['authorization_tos_pp']) && $api['authorization_tos_pp'] !== '') {
                $this->view->authorizationTosPp = html_entity_decode($api['authorization_tos_pp']);
                unset($api['authorization_tos_pp']);
            }

            $this->view->api = $api;

            if (isset($this->getData()['state'])) {
                $this->view->state = $this->getData()['state'];
            }

            return;
        } else if (isset($this->getData()['csrf'])) {//Authorize Flow 2
            $response = $this->api->checkAuthorizationLinkData($this->getData());

            if ($response && $response->getStatusCode() === 302) {
                $location = $response->getHeader('Location');

                if ($location && count($location) === 1) {
                    if ($this->api->clientRedirectUri === 'local') {
                        $location[0] = $location[0] . '&api_id=' . $this->api->api['id'];
                    }

                    return $this->response->redirect($location[0]);
                }
            }
        } else if (isset($this->request->getQuery()['code']) && isset($this->request->getQuery()['api_id']) ||
                   (isset($this->request->getQuery()['code']) && isset($this->request->getQuery()['state']) && isset($this->request->getQuery()['api_id']))
        ) {//Authorize Flow 3
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $api = $this->api->checkAuthorizationLinkData($this->request->getQuery());

            if (!$api) {
                $this->view->error = $this->api->packagesData->responseMessage;

                return;
            }

            $this->view->authorizationTosPp = null;
            if (isset($api['authorization_tos_pp']) && $api['authorization_tos_pp'] !== '') {
                unset($api['authorization_tos_pp']);
            }

            $this->view->api = $api;
            $this->view->client = $this->api->client;
            $this->view->code = $this->request->getQuery()['code'];
            if ($this->request->getQuery()['state']) {
                $this->view->state = $this->request->getQuery()['state'];
            }

            return;
        } else if (isset($this->getData()['client_id']) &&
                   (isset($this->getData()['refresh']) && $this->getData()['refresh'] == true)
        ) {
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $api = $this->api->checkAuthorizationLinkData($this->getData());

            if (!$api) {
                $this->view->error = $this->api->packagesData->responseMessage;

                return;
            }

            if (isset($api['authorization_tos_pp']) && $api['authorization_tos_pp'] !== '') {
                unset($api['authorization_tos_pp']);
            }

            $this->view->api = $api;

            $this->view->refresh = true;

            return;
        }

        $this->view->refresh = false;

        if (isset($this->getData()['api'])) {
            $api = $this->api->getById($this->getData()['api']);

            if (!$api ||
                ($api && ($api['status'] == false || $api && $api['registration_allowed'] == false))
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

        if ($this->access->auth->hasUserInSession() || $this->access->auth->hasRecaller()) {
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

    /**
     * Register client using web form.
     *
     * - Users enter their email address and select if they are registering for device API or user API.
     * ```
     * postData() params:
     * string  email           email address of the user
     * bool    device_id       true|false
     * ```
     */
    public function apiAddNewClientAction()
    {
        $this->requestIsPost();

        $clients = $this->api->init(true)->clients;

        $clients->addClient($this->postData(), true);

        $this->addResponse(
            $clients->packagesData->responseMessage,
            $clients->packagesData->responseCode,
            $clients->packagesData->responseData ?? []
        );
    }

    public function apiClientAction()
    {
        // trace([$this->postData()]);
        if (!isset($this->postData()['grant_type']) ||
            isset($this->postData()['grant_type']) && $this->postData()['grant_type'] === ''
        ) {
            $this->addResponse('Grant type not set.', 1);

            return;
        }

        if (!isset($this->postData()['client_id']) ||
            isset($this->postData()['client_id']) && $this->postData()['client_id'] === ''
        ) {
            $this->addResponse('Client ID not set.', 1);

            return;
        }

        if (!isset($this->postData()['client_secret']) ||
            isset($this->postData()['client_secret']) && $this->postData()['client_secret'] === ''
        ) {
            $this->addResponse('Client secret not set.', 1);

            return;
        }

        if ($this->postData()['grant_type'] === 'authorization_code' || $this->postData()['grant_type'] === 'refresh_token') {
            $apis = $this->api->init(true)->getApiInfo(false, true);

            foreach ($apis as $api) {
                if ($this->postData()['client_id'] === $api['client_id']) {
                    $this->api->api = $api;
                }
            }

            if ($this->api->api) {
                if ($this->postData()['grant_type'] === 'refresh_token') {
                    $this->api->setupApi(true);
                } else {
                    $this->api->setupApi();
                }
            }
        } else {
            $this->api->init(true)->setupApiViaClientId(true);

            $this->api->setupApi();
        }

        $this->api->registerClient();

        $this->addResponse(
            $this->api->packagesData->responseMessage,
            $this->api->packagesData->responseCode,
            $this->api->packagesData->responseData,
        );
    }
}
