<?php

namespace Applications\Dash\Components\Users\Profile;

use System\Base\BaseComponent;

class ProfileComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (!$this->auth->account()) {
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

            $this->view->responseCode = $this->profile->packagesData->responseCode;

            $this->view->responseMessage = $this->profile->packagesData->responseMessage;

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