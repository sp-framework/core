<?php

namespace Apps\Core\Components\Auth;

use System\Base\BaseComponent;

class AuthComponent extends BaseComponent
{
    public function viewAction()
    {
        $this->view->setLayout('auth');

        $this->view->canRegister = false;

        $this->view->canRecoverPassword = false;

        if ($this->app['registration_allowed'] && $this->app['registration_allowed'] == '1') {
            $this->view->canRegister = true;
        }
        if ($this->app['recover_password'] && $this->app['recover_password'] == '1') {
            $this->view->canRecoverPassword = true;
        }

        if (isset($this->session->needAgentAuth) && $this->session->needAgentAuth === true) {

            $this->setNeedAuthHeader();

            $this->view->pick('auth/agent');

            $this->session->needAgentAuth = false;

            return;
        }

        $domain = $this->domains->getDomain();

        if ($this->auth->check()) {
            if (isset($domain['exclusive_to_default_app']) &&
                $domain['exclusive_to_default_app'] == 1
            ) {
                return $this->response->redirect('/');
            } else {
                return $this->response->redirect('/' . strtolower($this->app['route']));
            }
        }

        if (isset($this->getData()['pwreset']) && $this->getData()['pwreset'] === 'true') {
            $this->view->coreSettings = $this->core->core['settings'];

            $this->view->canUse2fa = $this->auth->canUse2fa();

            $this->view->pick('auth/pwreset');

            return;
        } else if (isset($this->getData()['forgot']) && $this->getData()['forgot'] === 'password') {
            if ($this->app['recover_password'] == '0' || !$this->app['recover_password']) {
                $this->response->setStatusCode(404);

                return $this->response->send();

                exit;
            }

            $this->view->pick('auth/forgot');

            return;
        } else if (isset($this->getData()['setup2fa']) && $this->getData()['setup2fa'] === 'true') {

            $this->view->pick('auth/setup2fa');

            return;
        }

        $this->setNeedAuthHeader();

        if ($this->request->isAjax()) {
            $this->view->disable();
        }
    }

    protected function setNeedAuthHeader()
    {
        $this->response->setHeader('NEED_AUTH', '1');
        $this->response->setHeader('REDIRECT_URL', '/' . strtolower($this->app['route'] . '/auth'));
    }

    public function loginAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $auth = $this->auth->attempt($this->postData());

            if (isset($this->auth->packagesData->responseData)) {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode, $this->auth->packagesData->responseData);
            } else {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode);
            }

            if ($auth) {
                $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function logoutAction()
    {
        if ($this->auth->logout()) {
            $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;

            $this->addResponse('Ok');
        }
    }

    public function forgotAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->forgotPassword($this->postData());

            $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function pwresetAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->resetPassword($this->postData());

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;
            $this->view->responseCode = $this->auth->packagesData->responseCode;

            if (isset($this->auth->packagesData->redirectUrl)) {
                $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
            }
            if (isset($this->auth->packagesData->responseData)) {
                $this->view->responseData = $this->auth->packagesData->responseData;
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function sendVerificationAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->sendVerificationEmail();

            if (isset($this->auth->packagesData->responseData)) {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode, $this->auth->packagesData->responseData);
            } else {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function verifyAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->verifyVerficationCode($this->postData());

            $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function sendTwoFaEmailAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->sendTwoFaEmail($this->postData());

            if (isset($this->auth->packagesData->responseData)) {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode, $this->auth->packagesData->responseData);
            } else {
                $this->addResponse($this->auth->packagesData->responseMessage, $this->auth->packagesData->responseCode);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function checkPwStrengthAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->checkPwStrength($this->postData()['pass']) !== false) {
                $this->addResponse(
                    $this->auth->packagesData->responseMessage,
                    $this->auth->packagesData->responseCode,
                    $this->auth->packagesData->responseData
                );

                return;
            }

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode,
            );

        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function generatePwAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->generateNewPassword();

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode,
                $this->auth->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function enableTwoFaOtpAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->enableTwoFaOtp($this->postData())) {
                $this->view->provisionUrl = $this->auth->packagesData->provisionUrl;

                $this->view->qrcode = $this->auth->packagesData->qrcode;

                $this->view->secret = $this->auth->packagesData->secret;

                $this->view->responseMessage = $this->auth->packagesData->responseMessage;
            } else {
                $this->view->responseMessage = $this->auth->packagesData->responseMessage;
            }

            $this->view->responseCode = $this->auth->packagesData->responseCode;
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function verifyTwoFaOtpAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->verifyTwoFaOtp($this->postData())) {
                $this->view->redirectUrl = $this->links->url('/');
            }

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}