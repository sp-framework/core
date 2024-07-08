<?php

namespace Apps\Core\Middlewares\AgentCheck;

use System\Base\BaseMiddleware;

class AgentCheck extends BaseMiddleware
{
    public function process($data)
    {
        if (!$this->access->agent->checkAgent()) {
            $this->session->set('needAgentAuth', true);
            return $this->response->redirect($data['appRoute'] . '/auth');
        }
    }
}