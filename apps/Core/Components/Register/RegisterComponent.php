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
        if ($this->isJson()) {//For registering clients via Json (SP APIClientServices)
            $this->buildGetQueryParamsArr();
        }

        //Authorize Flow 1, test authorization redirect url
        if (isset($this->getData()['authorized']) &&
            !isset($this->getData()['response_type']) &&
            !isset($this->request->getQuery()['code'])
        ) {
            $this->view->refresh = false;
            $this->view->newToken = false;

            if (isset($this->getData()['test_authorized'])) {
                return true;
            }

            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            return;
        }

        if (isset($this->getData()['response_type']) &&
            $this->getData()['response_type'] === 'code' &&
            isset($this->getData()['client_id']) &&
            isset($this->getData()['redirect_uri']) &&
            isset($this->getData()['scope'])
        ) {//Authorize Flow 1, state is not set
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $apiArr = $this->api->checkAuthorizationLinkData($this->getData());

            if (!$apiArr) {
                if ($this->isJson()) {
                    $this->addResponse('Error:', 1, ['Error' => $this->api->packagesData->responseMessage]);

                    $this->sendJson();
                }

                $this->view->error = $this->api->packagesData->responseMessage;

                $this->view->refresh = false;
                $this->view->newToken = false;

                return;
            }

            $apiArr = $this->removeApiKeysInfo($apiArr);

            //Send Json to APIClientServices with authorization_url.
            if ($this->isJson()) {
                $this->addResponse('URL with state', 0, ['authorization_url' => $apiArr['authorization_url']]);

                $this->sendJson();
            }

            $this->view->authorizationTosPp = null;
            if (isset($apiArr['authorization_tos_pp']) && $apiArr['authorization_tos_pp'] !== '') {
                $this->view->authorizationTosPp = html_entity_decode($apiArr['authorization_tos_pp']);
                unset($apiArr['authorization_tos_pp']);
            }

            $this->view->api = $apiArr;

            if (isset($this->getData()['state'])) {
                $this->view->state = $this->getData()['state'];
            }

            $this->view->refresh = false;
            $this->view->newToken = false;

            return;
        } else if (isset($this->getData()['state'])) {//Authorize Flow 2
            $response = $this->api->checkAuthorizationLinkData($this->getData());

            if ($response && $response->getStatusCode() === 302) {
                $location = $response->getHeader('Location');

                if ($location && count($location) === 1) {
                    //Send Json to APIClientServices with authorization_url.
                    if ($this->isJson()) {
                        $responseData['registration_url'] = $this->links->url('register/apiClient');
                        $responseData['method'] = 'POST';
                        $responseData['code'] = explode('code=', $location[0])[1];

                        $this->addResponse('Code & authorization URL attached. Make call to registration_url with defined method to get access token.', 0, $responseData);

                        $this->sendJson();
                    }

                    if ($this->api->clientRedirectUri === 'local') {
                        $location[0] = $location[0] . '&api_id=' . $this->api->api['id'];
                    }

                    $this->view->refresh = false;
                    $this->view->newToken = false;

                    return $this->response->redirect($location[0]);
                }
            }
        } else if (isset($this->request->getQuery()['code']) && isset($this->request->getQuery()['api_id']) ||
                   (isset($this->request->getQuery()['code']) && isset($this->request->getQuery()['state']) && isset($this->request->getQuery()['api_id']))
        ) {//Authorize Flow 3
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $apiArr = $this->api->checkAuthorizationLinkData($this->request->getQuery());

            if (!$apiArr) {
                $this->view->error = $this->api->packagesData->responseMessage;

                return;
            }

            $this->view->authorizationTosPp = null;
            if (isset($apiArr['authorization_tos_pp']) && $apiArr['authorization_tos_pp'] !== '') {
                unset($apiArr['authorization_tos_pp']);
            }

            $this->view->api = $this->removeApiKeysInfo($apiArr);
            $this->view->client = $this->api->client;
            $this->view->code = $this->request->getQuery()['code'];

            if (isset($this->request->getQuery()['state'])) {
                $this->view->state = $this->request->getQuery()['state'];
            }

            $this->view->refresh = false;
            $this->view->newToken = false;

            return;
        } else if (isset($this->getData()['client_id']) &&
                   ((isset($this->getData()['refresh']) && $this->getData()['refresh'] == true) ||
                    (isset($this->getData()['new']) && $this->getData()['new'] == true))
        ) {//Token Generator using client and secret & Refresh token
            $this->view->setLayout('auth');

            $this->view->pick('register/authorization');

            $apiArr = $this->api->init(true)->checkAuthorizationLinkData($this->getData());

            if (!$apiArr) {
                $this->view->error = $this->api->packagesData->responseMessage;

                return;
            }

            if (isset($apiArr['authorization_tos_pp']) && $apiArr['authorization_tos_pp'] !== '') {
                unset($apiArr['authorization_tos_pp']);
            }

            $this->view->api = $this->removeApiKeysInfo($apiArr);

            $this->view->refresh = false;
            $this->view->newToken = false;
            if (isset($this->getData()['refresh']) && $this->getData()['refresh'] == true) {
                $this->view->refresh = true;
            }
            if (isset($this->getData()['new']) && $this->getData()['new'] == true) {
                $this->view->newToken = true;
            }

            $this->view->clientId =$this->getData()['client_id'];

            return;
        }

        if (isset($this->getData()['api'])) {
            $apiArr = $this->api->getById($this->getData()['api']);

            if (!$apiArr ||
                ($apiArr && ($apiArr['status'] == false || $apiArr && $apiArr['registration_allowed'] == false))
            ) {
                $this->response->setStatusCode(404);

                return $this->response->send();

                exit;
            }

            $this->view->setLayout('auth');

            $this->view->api = $this->removeApiKeysInfo($apiArr);

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

    protected function removeApiKeysInfo($apiArr)
    {
        unset($apiArr['private_key_passphrase']);
        unset($apiArr['private_key']);
        unset($apiArr['private_key_location']);

        return $apiArr;
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
        $this->validateData(data: $this->postData(), checkFields: ['grant_type', 'client_id', 'client_secret']);

        if ($this->postData()['grant_type'] === 'authorization_code' || $this->postData()['grant_type'] === 'refresh_token') {
            $apis = $this->api->init(true)->getApiInfo(false, true);

            foreach ($apis as $api) {
                if ($this->postData()['client_id'] === $api['client_id']) {
                    $this->api->api = $api;
                }
            }

            if ($this->api->api) {
                $this->api->init(true)->setupApiViaClientId(true);

                if ($this->postData()['grant_type'] === 'refresh_token') {
                    $this->api->setupApi(true);
                } else {
                    $this->api->setupApi();
                }
            }

            $this->addResponse('Incorrect client ID provided or API does not exist!', 1, []);

            return;
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
