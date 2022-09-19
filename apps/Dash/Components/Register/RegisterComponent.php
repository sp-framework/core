<?php

namespace Apps\Dash\Components\Register;

use Phalcon\Helper\Json;
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

            $postData = $this->postData();
            
            if (!$this->app['registration_allowed'] || $this->app['registration_allowed'] == '0') {
                $this->addResponse('Registration for this application is disabled. Please contact administrator.', 1);

                return;
            }            

            $postData['role_id'] = $this->app['registration_role_id'];
            $postData['email_new_password'] = '1';
            $postData['override_role'] = '0';
            $postData['permissions'] = Json::encode([]);
            $postData['force_pwreset'] = '1';
            $postData['status'] = '1';
            
            $canLogin = true;
            
            if ($this->app['approve_accounts_manually'] == '1') {
                $canLogin = false;
                $postData['status'] = '0';
            }

            $postData['can_login'] = Json::encode(
                [
                    strtolower($this->app['name']) => $canLogin
                ]
            );

            $validation = $this->accounts->validateData($postData);

            if ($validation === true) {
                $this->accounts->addAccount($postData);
            } else {
                $this->addResponse($validation, 1);

                return;
            }

            
            $this->addResponse($this->accounts->packagesData->responseMessage, $this->accounts->packagesData->responseCode);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}