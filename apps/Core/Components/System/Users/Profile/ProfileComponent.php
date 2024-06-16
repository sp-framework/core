<?php

namespace Apps\Core\Components\System\Users\Profile;

use System\Base\BaseComponent;

class ProfileComponent extends BaseComponent
{
    protected $profiles;

    public function initialize()
    {
        $this->profiles = $this->basepackages->profiles;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->getNewToken();

        $this->useStorage('private');

        if (isset($this->getData()['aid'])) {
            $profile = $this->profiles->generateViewData($this->getData()['aid']);
        } else {
            if (!$this->auth->account()) {
                return;
            }

            $profile = $this->profiles->generateViewData();
        }

        if ($profile) {
            $app = $this->apps->getAppInfo();

            $this->view->middlewares =
                msort(
                    $this->modules->middlewares->getMiddlewaresForAppType(
                        $app['app_type'],
                        $app['id']
                    ), 'sequence');

            $this->view->account = $this->profiles->packagesData->account;

            $this->view->profile = $this->profiles->packagesData->profile;

            $this->view->notifications_modules = $this->profiles->packagesData->notifications_modules;

            $this->view->subscriptions = $this->profiles->packagesData->subscriptions;

            $this->view->notifications = $this->profiles->packagesData->notifications;

            $this->view->canEmail = $this->profiles->packagesData->canEmail;

            $this->view->sessions = $this->profiles->packagesData->sessions;

            $this->view->coreSettings = $this->profiles->packagesData->coreSettings;

            $this->view->canUse2fa = $this->profiles->packagesData->canUse2fa;

            $apis = $this->api->getApiInfo(false, true);
            $passwordApis = [];
            if ($apis && count($apis) > 0) {
                foreach ($apis as $apiKey => $api) {
                    if (isset($api['grant_type']) && $api['grant_type'] === 'password') {
                        $passwordApis[$api['id']] = $api;
                    }
                }
            }
            $this->view->passwordApis = msort($passwordApis, 'id');
        } else {
            return;
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->profiles->updateProfile($this->postData());

            $this->addResponse(
                $this->profiles->packagesData->responseMessage,
                $this->profiles->packagesData->responseCode
            );
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

            $user['user'] = $this->auth->account()['email'];

            $user = array_merge($user, $this->postData());

            $this->auth->resetPassword($user, true);

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

    public function enableTwoFaOtpAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->enableTwoFaOtp()) {
                $this->view->provisionUrl = $this->auth->packagesData->provisionUrl;

                $this->view->qrcode = $this->auth->packagesData->qrcode;

                $this->view->secret = $this->auth->packagesData->secret;
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

            $this->auth->verifyTwoFaOtp($this->postData());

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function disableTwoFaOtpAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->disableTwoFaOtp($this->postData()['code']);

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function generateAvatarAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['avatarfile'])) {
                $generateAvatar = $this->profiles->generateAvatar($this->postData()['avatarfile']);
            } else if (isset($this->postData()['gender'])) {
                $generateAvatar = $this->profiles->generateAvatar(null, $this->postData()['gender']);
            } else {
                $generateAvatar = $this->profiles->generateAvatar();//Default Male
            }

            if ($generateAvatar) {
                $this->view->responseCode = $this->profiles->packagesData->responseCode;

                $this->view->avatar = $this->profiles->packagesData->avatar;

                $this->view->avatarName = $this->profiles->packagesData->avatarName;

                return;
            }

            $this->addResponse('Error Generating Avatar', 1);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function removeAccountAgentsAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->basepackages->accounts->removeAccountAgents($this->postData());

            $this->addResponse(
                $this->basepackages->accounts->packagesData->responseMessage,
                $this->basepackages->accounts->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}