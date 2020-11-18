<?php

namespace Applications\Admin\Components\Auth;

use System\Base\BaseComponent;

class AuthComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->setLayout('auth');
    }

    /**
     * @acl(name=login)
     */
    public function loginAction()
    {
        $auth = $this->auth->init();

        $auth->attempt($this->postData());

        $this->view->responseCode = $auth->packagesData->responseCode;

        $this->view->responseMessage = $auth->packagesData->responseMessage;
    }

    /**
     * @acl(name=logout)
     */
    public function logoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->responseCode = 0;
        }
    }
}