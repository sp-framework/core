<?php

namespace Applications\Admin\Components\Auth\Login;

use Phalcon\Assets\Inline;
use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BaseComponent;

class LoginComponent extends BaseComponent
{
    public function viewAction()
    {
        $this->view->setLayout('auth');
    }

    public function signinAction()
    {
        $validate = $this->validateData();

        if ($validate !== true) {
            $this->view->responseCode = 1;

            $this->view->responseMessage = $validate;

            return;
        }

        $attempt = $this->auth->attempt($this->postData());

        if ($attempt) {
            $this->view->responseCode = 0;

            $this->view->responseMessage = 'Authenticated. Redirecting...';
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Error: Username/Password incorrect!';
        }
    }

    protected function validateData()
    {
        $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid username."]);
        $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);

        $validated = $this->validation->validate($this->postData())->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    public function signoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->responseCode = 0;
        }
    }
}