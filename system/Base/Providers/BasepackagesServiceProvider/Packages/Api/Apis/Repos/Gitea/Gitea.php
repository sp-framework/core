<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Repos;

class Gitea extends Repos
{
    public function useMethod($method, $operation, $operationArr = [])
    {
        $this->initRemoteWebContent($method . ':' . $operation, $this->apiConfig);

        try {
            $class = $this->serviceClass . $method;

            $this->method = new $class($this->remoteWebContent, $this->config);

            return call_user_func_array([$this->method, $operation], $operationArr);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}