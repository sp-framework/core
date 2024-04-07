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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->accounts->registerAccount($this->postData())) {
                $this->view->redirectUrl = $this->accounts->packagesData->redirectUrl;
            }

            $this->addResponse($this->accounts->packagesData->responseMessage, $this->accounts->packagesData->responseCode);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function apiClientAction()
    {
        $this->api->registerClient($this->postData());
    }
}