<?php

namespace Apps\Core\Middlewares\IpFilter;

use Phalcon\Helper\Json;
use Phalcon\Mvc\View;
use System\Base\BaseMiddleware;

class IpFilter extends BaseMiddleware
{
    public function process($data)
    {
        if ($this->apps->ipFilter->checkList()) {
            return true;
        }

        $this->response->setStatusCode(404);

        $this->response->send();

        exit;
    }
}