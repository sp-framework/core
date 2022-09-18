<?php

namespace Apps\Dash\Middlewares\IpBlackList;

use Phalcon\Helper\Json;
use Phalcon\Mvc\View;
use System\Base\BaseMiddleware;

class IpBlackList extends BaseMiddleware
{
    public function process($data)
    {
        if ($this->apps->ipBlackList->checkList()) {
            return true;
        }

        $this->response->setStatusCode(404);

        $this->response->send();

        exit;
    }
}