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

    public $logEmail;

    protected $logsConfig;

    protected $session;

    protected $connection;

    protected $request;

    protected $email;

    protected $core;

    protected $customFormatter;

    protected $oneDbEntry = true;//True to make only 1 DB Entry

    public function __construct($logsConfig, $session, $connection, $request, $email, $core)
    {
        $this->logsConfig = $logsConfig;

        $this->session = $session;

        $this->connection = $connection;

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
                    $this->connection->getId(),
                    $this->request->getClientAddress()
                );

            if ($this->logsConfig->service === 'streamLogs') {
                if ($this->checkLogPath()) {
                    $savePath = base_path('var/log/');
                } else {
                    $savePath = '/tmp/';
                }

                $streamAdapter = new Stream($savePath . 'debug.log');
                $streamAdapter->setFormatter($this->customFormatter);

                // $adapter = ['stream'        => $streamAdapter];

                // if ($this->logsConfig->email) {
                //     $adapter = array_merge($adapter, $emailLogs);
                // }

                $this->log = new PhalconLogger(
                    'messages',
                    ['stream'        => $streamAdapter]
                );

                $this->log->getAdapter('stream')->begin();

            } else if ($this->logsConfig->service === 'dbLogs') {

                $dbAdapter = new DbAdapter($this->oneDbEntry);
                $dbAdapter->setFormatter($this->customFormatter);

                // $adapter = ['db'            => $dbAdapter];

                // if ($this->logsConfig->email) {
                //     $adapter = array_merge($adapter, $emailLogs);
                // }

                $this->log = new PhalconLogger(
                    'messages',
                    ['db'            => $dbAdapter]
                );

                $this->log->getAdapter('db')->begin();
            }
            $this->setLogLevel();

            if ($this->logsConfig->email) {
                $emailAdapter = new EmailAdapter($this->email, $this->core);
                $emailAdapter->setFormatter($this->customFormatter);

                $this->logEmail = new PhalconLogger(
                    'messages',
                    ['email'         => $emailAdapter]
                );

                $this->logEmail->getAdapter('email')->begin();
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
        if ($this->logsConfig->enabled) {
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
        }
    }

    public function commitEmail($message = null)
    {
        if ($this->logsConfig->enabled) {
            if ($this->logsConfig->email) {
                if ($message) {
                    $this->logEmail->emergency($message);
                }

                if ($this->logEmail->getAdapter('email')->inTransaction()) {
                    $this->logEmail->getAdapter('email')->commit();
                }

                $this->logEmail->getAdapter('email')->sendEmail();
            }
        } else {
            return 'logging is disabled';
        }
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