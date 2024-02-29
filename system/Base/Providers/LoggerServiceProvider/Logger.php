<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Logger\Logger as PhalconLogger;
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

    public $logExceptions;

    public $logEmail;

    protected $logsConfig;

    protected $session;

    protected $connection;

    protected $request;

    protected $helper;

    protected $email;

    protected $customFormatter;

    protected $oneDbEntry = true;//True to make only 1 DB Entry

    public function __construct($logsConfig, $session, $connection, $request, $helper, $email = null)
    {
        $this->logsConfig = $logsConfig;

        $this->session = $session;

        $this->connection = $connection;

        $this->request = $request;

        $this->email = $email;

        $this->helper = $helper;
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
                $this->request->getClientAddress(),
                $this->session->getId(),
                $this->connection->getId(),
                $this->helper,
                'c',
            );

        $streamAdapter = new Stream($savePath . 'exceptions.log');
        $streamAdapter->setFormatter($this->customFormatter);

        if ($this->logsConfig->exceptions) {
            $this->logExceptions = new PhalconLogger(
                'messages',
                ['exceptions'        => $streamAdapter]
            );

            $this->logExceptions->setLogLevel($this->logExceptions::ALERT);

            $this->logExceptions->getAdapter('exceptions')->begin();
        }

        if ($this->logsConfig->enabled) {
            if (PHP_SAPI === 'cli') {
                $streamAdapter = new Stream($savePath . 'cli.log');
                $streamAdapter->setFormatter($this->customFormatter);

                $this->log = new PhalconLogger(
                    'messages',
                    ['cli'        => $streamAdapter]
                );

                $this->log->getAdapter('cli')->begin();
            } else {
                if ($this->logsConfig->service === 'streamLogs') {
                    $streamAdapter = new Stream($savePath . 'debug.log');
                    $streamAdapter->setFormatter($this->customFormatter);

                    $this->log = new PhalconLogger(
                        'messages',
                        ['logs'        => $streamAdapter]
                    );

                    $this->log->getAdapter('logs')->begin();

                } else if ($this->logsConfig->service === 'dbLogs') {

                    $dbAdapter = new DbAdapter($this->oneDbEntry);
                    $dbAdapter->setFormatter($this->customFormatter);

                    $this->log = new PhalconLogger(
                        'messages',
                        ['db'            => $dbAdapter]
                    );

                    $this->log->getAdapter('db')->begin();
                }

                $this->setLogLevel();

                if ($this->email && $this->logsConfig->emergencyLogsEmail) {
                    $emailAdapter = new EmailAdapter($this->email, $this->logsConfig);
                    $emailAdapter->setFormatter($this->customFormatter);

                    $this->logEmail = new PhalconLogger(
                        'messages',
                        ['email'         => $emailAdapter]
                    );

                    $this->logEmail->getAdapter('email')->begin();
                }
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
        if ($this->logsConfig->exceptions) {
            if ($this->logsConfig->service === 'streamLogs') {
                if ($this->logExceptions->getAdapter('exceptions')->inTransaction()) {
                    $this->logExceptions->getAdapter('exceptions')->commit();
                }
            }
        }

        if ($this->logsConfig->enabled) {
            if (PHP_SAPI === 'cli') {
                if ($this->log->getAdapter('cli')->inTransaction()) {
                    $this->log->getAdapter('cli')->commit();
                }
            } else {
                if ($this->logsConfig->service === 'streamLogs') {
                    if ($this->log->getAdapter('logs')->inTransaction()) {
                        $this->log->getAdapter('logs')->commit();
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
    }

    public function commitEmail($message = null, $type = 'alert')
    {
        if ($this->logsConfig->emergencyLogsEmail) {
            if ($message) {
                $this->logEmail->$type($message);
            }

            if ($this->logEmail->getAdapter('email')->inTransaction()) {
                $this->logEmail->getAdapter('email')->commit();
            }

            return $this->logEmail->getAdapter('email')->sendEmail();
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

    public function getLogLevels()
    {
        return
            [
                [
                    'id'    => 'EMERGENCY',
                    'name'  => 'EMERGENCY'
                ],
                [
                    'id'    => 'CRITICAL',
                    'name'  => 'CRITICAL'
                ],
                [
                    'id'    => 'ALERT',
                    'name'  => 'ALERT'
                ],
                [
                    'id'    => 'ERROR',
                    'name'  => 'ERROR'
                ],
                [
                    'id'    => 'WARNING',
                    'name'  => 'WARNING'
                ],
                [
                    'id'    => 'NOTICE',
                    'name'  => 'NOTICE'
                ],
                [
                    'id'    => 'INFO',
                    'name'  => 'INFO'
                ],
                [
                    'id'    => 'DEBUG',
                    'name'  => 'DEBUG'
                ],
                [
                    'id'    => 'CUSTOM',
                    'name'  => 'CUSTOM'
                ],
            ];
    }
}