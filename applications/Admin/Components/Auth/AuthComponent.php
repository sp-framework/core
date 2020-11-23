<?php

namespace Applications\Admin\Components\Auth;

use System\Base\BaseComponent;

class AuthComponent extends BaseComponent
{
    public function viewAction()
    {
        if ($this->auth->check()) {
            return $this->response->redirect('/' . strtolower($this->application['route']));
        }

        $this->view->setLayout('auth');

        if (isset($this->getData()['pwreset']) && $this->getData()['pwreset'] === 'true') {

            $this->view->pick('auth/pwreset');

            return;
        } else if (isset($this->getData()['forgot']) && $this->getData()['forgot'] === 'password') {

            $this->view->pick('auth/forgot');

            return;
        }


        $this->response->setHeader('NEED_AUTH', '1');
        $this->response->setHeader('REDIRECT_URL', '/' . strtolower($this->application['route'] . '/auth'));

        if ($this->request->isAjax()) {
            $this->view->disable();
        }
    }

    public function loginAction()
    {
        $auth = $this->auth->attempt($this->postData());

        $this->view->responseCode = $this->auth->packagesData->responseCode;

        $this->view->responseMessage = $this->auth->packagesData->responseMessage;

        if ($auth) {
            $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
        }
    }

    public function logoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->responseCode = 0;
        }
    }

    public function forgotAction()
    {
        $this->auth->forgotPassword($this->postData());

        $this->view->responseCode = $this->auth->packagesData->responseCode;

        $this->view->responseMessage = $this->auth->packagesData->responseMessage;
    }

    public function pwresetAction()
    {
        $pwreset = $this->auth->resetPassword($this->postData());

        $this->view->responseCode = $this->auth->packagesData->responseCode;

        $this->view->responseMessage = $this->auth->packagesData->responseMessage;

        if ($pwreset) {
            $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
        }
    }
}