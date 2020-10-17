<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Logger as PhalconLogger;
use Phalcon\Logger\Adapter\Noop;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\Json;
use Phalcon\Logger\Formatter\Line;
use System\Base\Providers\LoggerServiceProvider\CustomFormat;
use System\Base\Providers\LoggerServiceProvider\Db\Adapter as DbAdapter;
use System\Base\Providers\LoggerServiceProvider\Email\Adapter as EmailAdapter;

class Logger
{
    public $log;

    protected $logsConfig;

    protected $session;

    protected $request;

    protected $email;

    protected $core;

    protected $customFormatter;

    protected $oneDbEntry = true;//True to make only 1 DB Entry

    public function __construct($logsConfig, $session, $request, $email, $core)
    {
        $this->logsConfig = $logsConfig;

        $this->session = $session;

        $this->request = $request;

        $this->email = $email;

        $this->core = $core;
    }

    public function init()
    {
        if ($this->logsConfig->enabled) {
            //Custom Formatter
            $this->customFormatter =
                new CustomFormat(
                    'c',
                    $this->session->getId(),
                    $this->request->getClientAddress()
                );

            //Email Adapter
            $emailAdapter = new EmailAdapter($this->email, $this->core);
            $emailAdapter->setFormatter($this->customFormatter);

            $emailLogs = ['email'         => $emailAdapter];

            if ($this->logsConfig->service === 'streamLogs') {
                if ($this->checkLogPath()) {
                    $savePath = base_path('var/log/');
                } else {
                    $savePath = '/tmp/';
                }

                $streamAdapter = new Stream($savePath . 'debug.log');
                $streamAdapter->setFormatter($this->customFormatter);

                $adapter = ['stream'        => $streamAdapter];

                if ($this->logsConfig->email) {
                    $adapter = array_merge($adapter, $emailLogs);
                }

                $this->log = new PhalconLogger('messages', $adapter);

                $this->log->getAdapter('stream')->begin();

            } else if ($this->logsConfig->service === 'dbLogs') {

                $dbAdapter = new DbAdapter($this->oneDbEntry);
                $dbAdapter->setFormatter($this->customFormatter);

                $adapter = ['db'            => $dbAdapter];

                if ($this->logsConfig->email) {
                    $adapter = array_merge($adapter, $emailLogs);
                }

                $this->log = new PhalconLogger('messages', $adapter);

                $this->log->getAdapter('db')->begin();
            }
            $this->setLogLevel();

            if ($this->logsConfig->email) {
                $this->log->getAdapter('email')->begin();
            }

        } else {
            //Blackhole
            $noopAdapter = new Noop();

            $this->log = new PhalconLogger(
                'messages',
                [
                    'noop'  => $noopAdapter
                ]
            );
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

            if ($this->log->getAdapter('stream')->inTransaction()) {
                $this->log->getAdapter('stream')->commit();
            }

        } else if ($this->logsConfig->service === 'dbLogs') {

            if ($this->oneDbEntry) {

                if ($this->log->getAdapter('db')->inTransaction()) {
                    $this->log->getAdapter('db')->commit();
                    $this->log->getAdapter('db')->addToDb();
                }
            } else {

                if ($this->log->getAdapter('db')->inTransaction()) {
                    $this->log->getAdapter('db')->commit();
                }
            }
        }

        if ($this->logsConfig->email) {
            if ($this->log->getAdapter('email')->inTransaction()) {
                $this->log->getAdapter('email')->commit();
            }

            $this->log->getAdapter('email')->sendEmail();
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