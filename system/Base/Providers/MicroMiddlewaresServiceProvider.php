<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;

class MicroMiddlewaresServiceProvider extends Injectable
{
    public function beforeExecuteRoute(
        Event $event,
        $micro,
    ) {
        if (!isset($this->apps->getAppInfo()['api_access']) ||
            (isset($this->apps->getAppInfo()['api_access']) && $this->apps->getAppInfo()['api_access'] == false)
        ) {
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setHeader('Cache-Control', 'no-store');

            if ($this->response->isSent() !== true) {
                $this->response->setJsonContent(['responseMessage' => 'API not available!', 'responseCode' => 1]);

                $this->response->send();
            }

            return false;
        }

        try {
            $this->api->setup($this->apps);
        } catch (\Exception $e) {
            throw $e;
        }

        // dump($this->api->apiNeedsAuth($this->core->core['settings']));die();
    }

    public function afterExecuteRoute(
        Event $event,
        $micro,
    ) {
        //
    }
}