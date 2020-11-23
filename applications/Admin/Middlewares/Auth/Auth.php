<?php

namespace Applications\Admin\Middlewares\Auth;

use System\Base\BaseMiddleware;

class Auth extends BaseMiddleware
{
    public function process()
    {
        $appName = strtolower($this->application['name']);
        $givenRoute = rtrim(explode('/q/', $this->request->getUri())[0], '/');

        $guestAccess =
        [
            '/' . $appName . '/auth',
            '/' . $appName . '/auth/login',
            '/' . $appName . '/auth/logout',
            '/' . $appName . '/auth/forgot',
            '/' . $appName . '/auth/pwreset'
        ];

        if (!in_array($givenRoute, $guestAccess)) {
            // Authenticate if in session
            if ($this->auth->hasUserInSession()) {
                try {
                    $this->auth->setUserFromSession();
                } catch (\Exception $e) {
                    // throw $e;
                    $this->auth->logout();
                }
            }

            //Authenticate via Cookie
            if ($this->auth->hasRecaller()) {
                try {
                    $this->auth->setUserFromCookie();
                } catch (\Exception $e) {
                    // throw $e;
                    $this->auth->logout();
                }
            }

            //Authenticated
            if (!$this->auth->check()) {
                return $this->response->redirect('/' . $appName . '/auth');
            }
        }
    }
}