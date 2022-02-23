<?php

namespace Apps\Dash\Middlewares\AgentCheck;

use Phalcon\Helper\Json;
use System\Base\BaseMiddleware;

class AgentCheck extends BaseMiddleware
{
    public function process()
    {
        $this->account = $this->auth->account();
        if (!$this->account) {
            $this->checkAgentCheckMiddlewareSequence();
        }

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
            $appRoute . '/auth/sendverification',
            $appRoute . '/auth/verify'
        ];

        if (in_array($givenRoute, $guestAccess) && $this->request->isGet()) {
            return $this->response->redirect($appRoute . '/auth');
        }

        if (!in_array($givenRoute, $guestAccess)) {
            //Browser Auth
            if (!$this->auth->checkAgent()) {
                $this->session->set('needAgentAuth', true);
                return $this->response->redirect($appRoute . '/auth');
            }

            return true;
        }

        return false;
    }

    protected function checkAgentCheckMiddlewareSequence()
    {
        $appId = $this->apps->getAppInfo()['id'];

        $agentCheck = $this->modules->middlewares->getNamedMiddlewareForApp('AgentCheck', $appId);
        $agentCheckSequence = (int) $agentCheck['apps'][$appId]['sequence'];

        $auth = $this->modules->middlewares->getNamedMiddlewareForApp('Auth', $appId);
        $authSequence = (int) $auth['apps'][$appId]['sequence'];

        if ($agentCheckSequence < $authSequence) {
            $agentCheck['apps'][$appId]['sequence'] = 98;
            $agentCheck['apps'] = Json::encode($agentCheck['apps']);
            $this->modules->middlewares->update($agentCheck);

            throw new \Exception('AgentCheck middleware sequence is lower then Auth middleware sequence, which is wrong. You need to authenticate before we can the agent. I have fixed the problem by changing the agent middleware sequence to 98.');
        }
    }
}