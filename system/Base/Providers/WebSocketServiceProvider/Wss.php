<?php

namespace System\Base\Providers\WebSocketServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation as PhalconValidation;
use ZMQContext;

class Wss
{
    protected $connector = null;

    protected $context;

    protected $socket;

    public function init()
    {
        return $this;
    }

    public function setConnector($connector = null)
    {
        if ($connector) {
            $this->connector = $connector;

            return;
        }

        $this->connector = 'tcp://localhost:5555';
    }

    public function getConnector()
    {
        return $this->connector;
    }

    public function setContext()
    {
        $this->context = new ZMQContext();
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setSocket($contextName = 'New Notification')
    {
        if (!$this->context) {
            $this->setContext();
        }

        $this->socket = $this->context->getSocket(\ZMQ::SOCKET_PUSH, $contextName);
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function send(array $data, $contextName = 'New Notification')
    {
        if (!$this->connector) {
            $this->setConnector();
        }

        if (!$this->socket) {
            $this->setSocket($contextName);
        }

        $this->socket->connect($this->connector);

        $this->socket->send(Json::encode($data));
    }
}