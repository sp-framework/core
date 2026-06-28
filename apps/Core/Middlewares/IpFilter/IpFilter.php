<?php

namespace Apps\Core\Middlewares\IpFilter;

use System\Base\BaseMiddleware;

class IpFilter extends BaseMiddleware
{
    public function process($data)
    {
        return true;
        try {
            if ($this->access->ipFilter->checkList()) {
                return true;
            }
        } catch (\throwable $e) {
            trace([$e]);
            $this->logger->log->debug('Error while checking for IP Filter List: ' . $e->getMessage() . '. Allowing unconditionally.');

            return true;
        }

        $this->logger->commit();

        $this->response->setStatusCode(404);

        $this->response->send();

        exit;
    }
}