<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Logger as PhalconLogger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\Json;
use Phalcon\Logger\Formatter\Line;
use System\Base\Providers\LoggerServiceProvider\CustomFormat;
use System\Base\Providers\LoggerServiceProvider\Db\Adapter;

class Logger
{
    private $container;

    public $log;

    protected $config;

    protected $customFormatter;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->config = $this->container->getShared('config');
    }

    public function init()
    {
        if ($this->checkLogPath()) {
            $savePath = base_path('var/log/');
        } else {
            $savePath = '/tmp/';
        }

        $this->customFormatter =
            new CustomFormat(
                'c',
                $this->container->getShared('session')->getId(),
                $this->container->getShared('config')->debug,
            );

        $debugAdapter = new Stream($savePath . 'debug.log');
        $debugAdapter->setFormatter($this->customFormatter);

        $dbAdapter = new Adapter($this->container);
        $dbAdapter->setFormatter($this->customFormatter);

        if ($this->config->debug) {
            $this->log = new PhalconLogger(
                'messages',
                [
                    'debug'     => $debugAdapter
                ]
            );

            $this->log->getAdapter('debug')->begin();

        } else {
            $this->log = new PhalconLogger(
                'messages',
                [
                    'system'    => $dbAdapter
                ]
            );

            $this->log->setLogLevel($this->log::INFO);

            $this->log->getAdapter('system')->begin();
        }

        return $this;
    }

    protected function checkLogPath()
    {
        if (!is_dir(base_path('var/log/'))) {
            if (!mkdir(base_path('var/log/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }

    public function commit()
    {
        if ($this->config->debug) {
            $this->log->getAdapter('debug')->commit();
        } else {
            $this->log->getAdapter('system')->commit();
        }
    }

    public function getConnectionId()
    {
        return $this->customFormatter->getConnectionId();
    }
}