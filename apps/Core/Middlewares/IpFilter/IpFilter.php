<?php

namespace Apps\Core\Middlewares\IpFilter;

use System\Base\BaseMiddleware;

class IpFilter extends BaseMiddleware
{
    public function process($data)
    {
        // return true;
        try {
            if ($this->access->ipFilter->checkIp()) {
                return true;
            }
        } catch (\throwable $e) {
            trace([$e]);
            $this->logger->logIpFilters->alert('Error while checking for IP Filter List: ' . $e->getMessage() . '. Allowing unconditionally.');

            return true;
        }

        $filterSettings = $this->access->ipFilter->getIpFilterSettings();

        $responseType = 'code';

        if (isset($filterSettings['response_type'])) {
            $responseType = $filterSettings['response_type'];
        }

        $this->logger->commit();

        if ($responseType === 'code') {
            $this->response->setStatusCode($filterSettings['response_type'] ?? 404);
        } else if ($responseType === 'url') {
            $url = '/';

            if (isset($filterSettings['response_url']) && $filterSettings['response_url'] !== '') {
                $url = $filterSettings['response_url'];
            }

            return $this->response->redirect($url);
        } else if ($responseType === 'route') {
            if (isset($filterSettings['response_route']) && $filterSettings['response_route'] !== '') {
                // $filterSettings['response_route'] = 'home';
                $routeUrl = $this->helper->last(explode('/', $this->request->getURI()));

                if ($routeUrl === $filterSettings['response_route']) {
                    return true;
                }

                return $this->response->redirect($this->links->url($filterSettings['response_route']));
            }
        }

        $this->response->send();

        exit;
    }
}