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

        $givenRoute = strtolower(rtrim(explode('/q/', $this->request->getUri())[0], '/'));

        $guestAccess =
        [
            $appRoute . '/auth',
            $appRoute . '/auth/login',
            $appRoute . '/auth/logout',
            $appRoute . '/auth/forgot',
            $appRoute . '/auth/pwreset',
            $appRoute . '/auth/sendverification',
            $appRoute . '/auth/verify'
        ];

        if (!in_array($givenRoute, $guestAccess)) {
            // Authenticate if in session
            if ($this->auth->hasUserInSession()) {
                try {
                    $this->auth->setUserFromSession();

                    if (!$this->auth->hasRecaller()) {
                        $this->session->set('redirectUrl', $this->request->getUri());
                        return $this->response->redirect($appRoute . '/auth');
                    }

                    $this->auth->checkRecaller();
                } catch (\Exception $e) {
                    $this->auth->logout();

                    return false;
                }
            }

            //Authenticated
            if (!$this->auth->check()) {
                $this->session->set('redirectUrl', $this->request->getUri());
                return $this->response->redirect($appRoute . '/auth');
            }

            //Browser Auth
            if (!$this->auth->checkAgent()) {
                $this->session->set('needAgentAuth', true);
                return $this->response->redirect($appRoute . '/auth');
            }

            return true;
        }

        return false;
    }
}