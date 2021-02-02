<?php

namespace Apps\Dash\Middlewares\Auth;

use System\Base\BaseMiddleware;

class Auth extends BaseMiddleware
{
    public function process()
    {
        $domain = $this->domains->getDomain();

        if (isset($domain['exclusive_to_default_app']) &&
            $domain['exclusive_to_default_app'] == 1
        ) {
            $appRoute = '';
        } else {
            $appRoute = '/' . strtolower($this->app['route']);
        }

        $givenRoute = rtrim(explode('/q/', $this->request->getUri())[0], '/');

        $guestAccess =
        [
            $appRoute . '/auth',
            $appRoute . '/auth/login',
            $appRoute . '/auth/logout',
            $appRoute . '/auth/forgot',
            $appRoute . '/auth/pwreset'
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
                return $this->response->redirect($appRoute . '/auth');
            }

            return true;
        }
        return false;
    }
}