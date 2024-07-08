<?php

namespace Apps\Core\Middlewares\Auth;

use System\Base\BaseMiddleware;

class Auth extends BaseMiddleware
{
    public function process($data)
    {
        //Authenticate API
        if ($this->api->isApi()) {
            return $this->api->authCheck();
        }

        // Authenticate if in session
        if ($this->access->auth->hasUserInSession()) {
            try {
                $this->access->auth->setUserFromSession();
            } catch (\Exception $e) {
                $this->access->auth->logout();
            }
        }

        //Authenticate via Cookie
        if ($this->access->auth->hasRecaller()) {
            try {
                $this->access->auth->setUserFromRecaller();
            } catch (\Exception $e) {
                $this->access->auth->logout();
            }
        }

        //Authenticated
        if (!$this->access->auth->check()) {
            $this->session->set('redirectUrl', $this->request->getUri());
            return $this->response->redirect($data['appRoute'] . '/auth');
        }
    }
}