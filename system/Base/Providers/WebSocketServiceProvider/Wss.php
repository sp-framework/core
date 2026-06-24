<?php

namespace System\Base\Providers\WebSocketServiceProvider;

use ZMQContext;

class Wss
{
    protected $config;

    protected $connector = null;

    protected $context;

    protected $socket;

    protected $helper;

    protected $logger;

    public function __construct($config, $helper, $logger = null)
    {
        $this->config = $config;

        $this->helper = $helper;

        $this->logger = $logger;
    }

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

        if ($this->config->websocket->port == '0' || $this->config->websocket->port === '' && $this->logger) {
            $this->logger->log->debug(
                'Websocket is configured to connect on port ' .
                $this->config->websocket->port .
                '. Standard port is 5555, please verify ZMQ and core settings.'
            );
        }

        $this->connector =
            $this->config->websocket->protocol .
            '://' .
            $this->config->websocket->host .
            ':' .
            $this->config->websocket->port;
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

    public function setSocket($contextName = 'SPWss')
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

    public function send(array $data, $contextName = 'SPWss')
    {
        if (!$this->connector) {
            $this->setConnector();
        }

        if (!$this->socket) {
            $this->setSocket($contextName);
        }

        $this->socket->connect($this->connector);

        $this->socket->send($this->helper->encode($data));

        return true;
    }
}