<?php

namespace Apps\Core\Middlewares\IpFilter;

use System\Base\BaseMiddleware;

class IpFilter extends BaseMiddleware
{
    public function process($data)
    {
        try {
            if ($this->access->ipFilter->checkIp()) {
                return true;
            }

            $this->access->ipFilter->processMiddlewareResponse();
        } catch (\throwable $e) {
            $this->logException($e);

            $this->logger->logIpFilters->alert('Error while checking for IP Filter List: ' . $e->getMessage() . '. Allowing unconditionally.');

            return true;
        }
    }
}