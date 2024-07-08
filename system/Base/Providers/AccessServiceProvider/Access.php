<?php

namespace System\Base\Providers\AccessServiceProvider;

use System\Base\Providers\AccessServiceProvider\Access\Acl;
use System\Base\Providers\AccessServiceProvider\Access\Agent;
use System\Base\Providers\AccessServiceProvider\Access\Auth;
use System\Base\Providers\AccessServiceProvider\Access\IpFilter;

class Access
{
    protected $auth;

    protected $acl;

    protected $agent;

    protected $ipFilter;

    public function __construct()
    {
    }

    public function __get($name)
    {
        if (!isset($this->{$name})) {
            if (method_exists($this, $method = "init" . ucfirst("{$name}"))) {
                $this->{$name} = $this->{$method}();
            }
        }

        return $this->{$name};
    }

    protected function initAuth()
    {
        $this->auth = (new Auth())->init();

        return $this->auth;
    }

    protected function initAcl()
    {
        $this->acl = (new Acl())->init();

        return $this->acl;
    }

    protected function initAgent()
    {
        $this->agent = (new Agent())->init();

        return $this->agent;
    }

    protected function initIpFilter()
    {
        $this->ipFilter = (new IpFilter())->init();

        return $this->ipFilter;
    }
}