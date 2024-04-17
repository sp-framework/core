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