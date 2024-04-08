<?php

namespace Apps\Core\Middlewares\Auth;

use System\Base\BaseMiddleware;

class Auth extends BaseMiddleware
{
    public function process($data)
    {
        //Authenticate API
        if ($this->api->isApi($this->request)) {
            return $this->api->check($this->apps);
        }

        // Authenticate if in session
        if ($this->auth->hasUserInSession()) {
            try {
                $this->auth->setUserFromSession();
            } catch (\Exception $e) {
                $this->auth->logout();
            }
        }

        //Authenticate via Cookie
        if ($this->auth->hasRecaller()) {
            try {
                $this->auth->setUserFromRecaller();
            } catch (\Exception $e) {
                $this->auth->logout();
            }
        }

        //Authenticated
        if (!$this->auth->check()) {
            $this->session->set('redirectUrl', $this->request->getUri());
            return $this->response->redirect($data['appRoute'] . '/auth');
        }
    }
}