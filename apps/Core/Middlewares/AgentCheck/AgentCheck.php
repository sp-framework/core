<?php

namespace Apps\Core\Middlewares\AgentCheck;

use System\Base\BaseMiddleware;

class AgentCheck extends BaseMiddleware
{
    public function process($data)
    {
        $this->account = $this->auth->account();

        //Browser Auth
        if (!$this->auth->checkAgent()) {
            $this->session->set('needAgentAuth', true);
            return $this->response->redirect($data['appRoute'] . '/auth');
        }
    }
}