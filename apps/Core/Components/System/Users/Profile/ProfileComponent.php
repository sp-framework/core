<?php

namespace Apps\Core\Components\System\Users\Profile;

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

        $this->getNewToken();

        $this->useStorage('private');

        $profile = $this->profile->generateViewData();

        if ($profile) {
            $app = $this->apps->getAppInfo();

            $this->view->middlewares =
                msort(
                    $this->modules->middlewares->getMiddlewaresForAppType(
                        $app['app_type'],
                        $app['id']
                    ), 'sequence');

            $this->view->profile = $this->profile->packagesData->profile;

            $this->view->packages = $this->profile->packagesData->packages;

            $this->view->subscriptions = $this->profile->packagesData->subscriptions;

            $this->view->notifications = $this->profile->packagesData->notifications;

            $this->view->canEmail = $this->profile->packagesData->canEmail;

            $this->view->sessions = $this->profile->packagesData->sessions;
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

            $this->profile->updateProfile($this->postData());

            $this->addResponse(
                $this->profile->packagesData->responseMessage,
                $this->profile->packagesData->responseCode
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
            $this->addResponse('Method Not Allowed', 1);
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
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function verifyTwoFaAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->enableVerifyTwoFa($this->postData());

            $this->addResponse(
                $this->auth->packagesData->responseMessage,
                $this->auth->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function disableTwoFaAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->auth->disableTwoFa($this->postData()['code']);

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