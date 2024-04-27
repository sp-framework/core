<?php

namespace System\Base\Providers\TerminalServiceProvider;

use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;
use System\Base\Providers\TerminalServiceProvider\CliTable\CliTable;

class Terminal extends BasePackage
{
    protected $banner;

    protected $account = null;

    protected $whereAt = 'disable';

    protected $hostname = 'sp';

    protected $prompt = '> ';

    protected $exit = 'Bye!';

    protected $commands;

    protected $sessionTimeout = 3600;

    protected $loginAt;

    protected $hasChilds = false;

    public function init()
    {
        try {
            $this->commands = $this->helper->decode($this->localContent->read('system/Base/Providers/TerminalServiceProvider/Commands.json'), true);

            $this->apps->getAppInfo('core');
        } catch (\throwable | UnableToReadFile $e) {
            \cli\line("%W%1Error Loading commands, contact Developer!\n\n");

            exit;
        }

        $this->setBanner();

        echo $this->banner;

        return $this;
    }

    public function run()
    {
        echo $this->hostname . $this->prompt;

        if ($this->hasChilds) {
            $command = $this->hasChilds;

            $this->hasChilds = false;
        } else {
            $cliHandle = fopen('php://stdin','r');

            $command = rtrim(fgets($cliHandle), "\r\n");
        }

        while (true) {
            if ($command === 'exit') {
                if ($this->whereAt === 'disable') {
                    break;
                } else {
                    $this->account = null;
                    $this->whereAt = 'disable';
                    $this->prompt = '> ';
                }
            } else if (str_contains($command, '?') || $command === '?' || $command === 'help') {
                if ($command === '?') {
                    var_dump('me');
                    $this->showHelp();
                } else if (str_contains($command, '?')) {
                    $this->showHelp(true);

                    $command = str_replace('?', '', $command);

                    $this->hasChilds = $command;
                }

            } else if (checkCtype($command)) {
                if (!$this->searchCommand(trim(strtolower($command)))) {
                    echo "Command " . $command . " not found!\n";
                }
            }

            $this->run();
        }

        if ($this->logger) {
            $this->logger->commit();
        }

        \cli\line('');
        \cli\line('Bye!');
        \cli\line('');

        exit;
    }

    protected function showHelp($hasChilds = false)
    {
        $availableCommands = [];

        foreach ($this->commands as $command) {
            if ($this->whereAt === $command['availableAt']) {
                if (!$hasChilds && $command['hasChilds'] === false) {
                    array_push($availableCommands, [$command['command'], $command['description']]);
                } else if ($hasChilds && $command['hasChilds'] === true) {
                    var_dump($command);
                    array_push($availableCommands, [$command['command'], $command['description']]);
                }
            }
        }

        if (count($availableCommands) > 0) {
            $table = new \cli\Table();
            $table->setHeaders(['Available Commands', 'Description']);
            $table->setRows($availableCommands);
            $table->setRenderer(new \cli\table\Ascii([25, 100]));
            $table->display();
        }
    }

    protected function searchCommand($command)
    {
        if (isset($this->commands[$command])) {
            if (isset($this->commands[$command]['hasChilds']) && $this->commands[$command]['hasChilds'] === true) {
                var_dump($command);
                return true;
            }

            return $this->execCommand($this->commands[$command]);
        }

        return false;
    }

    protected function execCommand($commandArr)
    {
        if (!isset($commandArr['class'])) {
            return false;
        }

        $class = new $commandArr['class'];

        $response = $class->init($this)->run($commandArr['args']);

        if ($response !== null) {
            if ($class->packagesData->responseCode == 0) {
                $color = "%G";
            } else {
                $color = "%R";
            }

            \cli\line($color . $class->packagesData->responseMessage);
            \cli\out("%W");

            return true;
        }
    }


    protected function setBanner($banner = null)
    {
        $this->banner =
            "Welcome to SP!\n" .
            "Type help or ? (question mark) for help at any time\n";
    }

    public function setWhereAt($at)
    {
        $this->whereAt = $at;
    }

    public function getWhereAt()
    {
        return $this->whereAt;
    }

    public function setSessionTimeout($timeout)
    {
        if ($timeout > 86400) {
            $timeout = 86400;
        }

        $this->sessionTimeout = $timeout;
    }

    public function getSessionTimeout()
    {
        return $this->sessionTimeout;
    }

    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
    }

    public function getPrompt()
    {
        return $this->prompt;
    }

    public function setLoginAt($time)
    {
        $this->loginAt = $time;
    }

    public function getLoginAt()
    {
        return $this->loginAt;
    }

    public function setAccount($account)
    {
        $this->account = $account;
    }

    public function getAccount()
    {
        return $this->account;
    }
}