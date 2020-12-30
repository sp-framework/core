<?php

namespace Applications\Ecom\Dashboard\Components\Auth;

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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $auth = $this->auth->attempt($this->postData());

            $this->view->responseCode = $this->auth->packagesData->responseCode;

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;

            if ($auth) {
                $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->forgotPassword($this->postData());

            $this->view->responseCode = $this->auth->packagesData->responseCode;

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function pwresetAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $pwreset = $this->auth->resetPassword($this->postData());

            $this->view->responseCode = $this->auth->packagesData->responseCode;

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;

            if ($pwreset) {
                $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}