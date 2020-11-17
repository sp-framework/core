<?php

namespace Applications\Admin\Components\Auth\Login;

use System\Base\BaseComponent;

class LoginComponent extends BaseComponent
{
    public function viewAction()
    {
        $this->view->setLayout('auth');
    }

    public function signinAction()
    {
        $auth = $this->auth->init();

        $auth->attempt($this->postData());

        $this->view->responseCode = $auth->packagesData->responseCode;

        $this->view->responseMessage = $auth->packagesData->responseMessage;
    }

    public function signoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->responseCode = 0;
        }
    }
}