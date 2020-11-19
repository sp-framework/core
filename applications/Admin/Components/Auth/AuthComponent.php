<?php

namespace Applications\Admin\Components\Auth;

use System\Base\BaseComponent;

class AuthComponent extends BaseComponent
{
    public function viewAction()
    {
        if ($this->auth->check()) {
            return $this->response->redirect('/' . strtolower($this->application['name']));
        }
        $this->response->setHeader('NEED_AUTH', '1');
        $this->response->setHeader('REDIRECT_URL', '/' . strtolower($this->application['name'] . '/auth'));

        $this->view->setLayout('auth');

        if ($this->request->isAjax()) {
            $this->view->disable();
        }
    }

    public function loginAction()
    {
        $auth = $this->auth->init();

        $auth->attempt($this->postData());

        $this->view->responseCode = $auth->packagesData->responseCode;

        $this->view->responseMessage = $auth->packagesData->responseMessage;
    }

    public function logoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->responseCode = 0;
        }
    }
}