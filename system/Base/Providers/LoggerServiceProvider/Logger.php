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

    protected $logsConfig;

    protected $customFormatter;

    protected $dbEntryCount = true;//True to make only 1 DB Entry

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->logsConfig = $this->container->getShared('config')->logs;
    }

    public function init()
    {
        $this->customFormatter =
            new CustomFormat(
                'c',
                $this->container->getShared('session')->getId(),
                $this->container->getShared('request')->getClientAddress()
            );

        if ($this->logsConfig->service === 'streamLogs') {
            if ($this->checkLogPath()) {
                $savePath = base_path('var/log/');
            } else {
                $savePath = '/tmp/';
            }

            $streamAdapter = new Stream($savePath . 'debug.log');
            $streamAdapter->setFormatter($this->customFormatter);

            $this->log = new PhalconLogger(
                'messages',
                [
                    'stream'     => $streamAdapter
                ]
            );

            $this->log->getAdapter('stream')->begin();

        } else if ($this->logsConfig->service === 'dbLogs') {

            $dbAdapter = new Adapter($this->container, $this->dbEntryCount);
            $dbAdapter->setFormatter($this->customFormatter);

            $this->log = new PhalconLogger(
                'messages',
                [
                    'db'    => $dbAdapter
                ]
            );

            $this->setLogLevel();

            $this->log->getAdapter('db')->begin();
        }
        //Email logging for Emergency, Critical & Alerts needs to be configured using phpmailer
        //Emergency should be for Exceptions
        //Critical for Performance Degrade (Need a method to calculate DB times, etc)
        //Alert for any Disk related messages.

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
        if ($this->logsConfig->service === 'streamLogs') {

            $this->log->getAdapter('stream')->commit();

        } else if ($this->logsConfig->service === 'dbLogs') {

            if ($this->dbEntryCount) {

                $this->log->getAdapter('db')->commit();
                $this->log->getAdapter('db')->addToDb();

            } else {

                $this->log->getAdapter('db')->commit();

            }
        }
    }

    public function getConnectionId()
    {
        return $this->customFormatter->getConnectionId();
    }

    protected function setLogLevel()
    {
        switch ($this->logsConfig->level) {
            case 'EMERGENCY':
                $this->log->setLogLevel($this->log::EMERGENCY);
                break;
            case 'CRITICAL':
                $this->log->setLogLevel($this->log::CRITICAL);
                break;
            case 'ALERT':
                $this->log->setLogLevel($this->log::ALERT);
                break;
            case 'ERROR':
                $this->log->setLogLevel($this->log::ERROR);
                break;
            case 'WARNING':
                $this->log->setLogLevel($this->log::WARNING);
                break;
            case 'NOTICE':
                $this->log->setLogLevel($this->log::NOTICE);
                break;
            case 'INFO':
                $this->log->setLogLevel($this->log::INFO);
                break;
            case 'DEBUG':
                $this->log->setLogLevel($this->log::DEBUG);
                break;
            case 'CUSTOM':
                $this->log->setLogLevel($this->log::CUSTOM);
                break;
            default:
                $this->log->setLogLevel($this->log::INFO);
                break;
        }
    }
}