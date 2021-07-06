<?php

namespace Apps\Dash\Components\System\Users\Profile;

use System\Base\BaseComponent;

class ProfileComponent extends BaseComponent
{
    protected $profile;

    public function initialize()
    {
        $this->profile = $this->basepackages->profile;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (!$this->auth->account()) {
            return;
        }

        $profile = $this->profile->generateViewData();

        if ($profile) {
            $this->view->packages = $this->profile->packagesData->packages;

            $this->view->subscriptions = $this->profile->packagesData->subscriptions;

            $this->view->notifications = $this->profile->packagesData->notifications;

            $this->view->canEmail = $this->profile->packagesData->canEmail;
        }

        $this->getNewToken();

        $this->useStorage('private');
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

            $this->profile->updateProfile($this->postData());

            $this->view->responseCode = $this->profile->packagesData->responseCode;

            $this->view->responseMessage = $this->profile->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function checkPwStrengthAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->checkPwStrength($this->postData()['pass'])) {
                $this->view->responseData = $this->auth->packagesData->responseData;
            }

            $this->view->responseCode = $this->auth->packagesData->responseCode;

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function generatePwAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->generateNewPassword();

            $this->view->responseData = $this->auth->packagesData->responseData;

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

            if ($this->postData()['pass'] === $this->postData()['newpass']) {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Current and new password are same!';

                return;
            }

            $user['user'] = $this->auth->account()['email'];

            $user = array_merge($user, $this->postData());

            $pwreset = $this->auth->resetPassword($user, true);

            if ($pwreset) {
                $this->view->redirectUrl = $this->auth->packagesData->redirectUrl;
            } else {
                $this->view->responseMessage = $this->auth->packagesData->responseMessage;
            }

            $this->view->responseCode = $this->auth->packagesData->responseCode;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function enableTwoFaAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->auth->enableTwoFa()) {
                $this->view->provisionUrl = $this->auth->packagesData->provisionUrl;

                $this->view->qrcode = $this->auth->packagesData->qrcode;

                $this->view->secret = $this->auth->packagesData->secret;
            } else {
                $this->view->responseMessage = $this->auth->packagesData->responseMessage;
            }

            $this->view->responseCode = $this->auth->packagesData->responseCode;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function verifyTwoFaAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->enableVerifyTwoFa($this->postData()['code']);

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;

            $this->view->responseCode = $this->auth->packagesData->responseCode;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function disableTwoFaAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->disableTwoFa($this->postData()['code']);

            $this->view->responseMessage = $this->auth->packagesData->responseMessage;

            $this->view->responseCode = $this->auth->packagesData->responseCode;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function generateAvatarAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['avatarfile'])) {
                $generateAvatar = $this->profile->generateAvatar($this->postData()['avatarfile']);
            } else if (isset($this->postData()['gender'])) {
                $generateAvatar = $this->profile->generateAvatar(null, $this->postData()['gender']);
            } else {
                $generateAvatar = $this->profile->generateAvatar();//Default Male
            }

            if ($generateAvatar) {
                $this->view->responseCode = $this->profile->packagesData->responseCode;

                $this->view->avatar = $this->profile->packagesData->avatar;

                $this->view->avatarName = $this->profile->packagesData->avatarName;

                return;
            }

            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Error Generating Avatar';

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}