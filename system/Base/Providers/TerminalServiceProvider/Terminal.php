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

        system('clear');

        $this->setBanner();

        \cli\line($this->banner);

        return $this;
    }

    public function run()
    {
        echo $this->hostname . $this->prompt;

        $cliHandle = fopen('php://stdin','r');

        $command = rtrim(fgets($cliHandle), "\r\n");

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
                    $this->showHelp($this->commands);
                } else if (str_contains($command, '?')) {
                    $command = trim(str_replace('?', '', $command));
                    $command = explode(' ', $command);

                    $commands = $this->commands;

                    if (count($command) > 1) {
                        $cmdPath = [];
                        foreach ($command as $commandKey) {
                            if (isset($commands[$commandKey]['childs']) &&
                                is_array($commands[$commandKey]['childs']) &&
                                count($commands[$commandKey]['childs']) > 0
                            ) {
                                array_push($cmdPath, $commandKey);

                                $commands = $commands[$commandKey]['childs'];
                            }
                        }

                        if (isset($commands[$this->helper->last($command)])) {
                            $this->showHelp([$commands[$this->helper->last($command)]], $cmdPath);
                        } else {
                            $this->showHelp($commands, $cmdPath);
                        }
                    } else {
                        if (isset($this->commands[$command[0]]) &&
                            isset($this->commands[$command[0]]['childs']) &&
                            is_array($this->commands[$command[0]]['childs']) &&
                            count($this->commands[$command[0]]['childs']) > 0
                        ) {
                            $this->showHelp($this->commands[$command[0]]['childs'], [$command[0]]);
                        } else if (isset($this->commands[$command[0]])) {
                            $this->showHelp($this->commands, [$command[0]]);
                        } else {
                            if (!$this->searchCommand(trim(strtolower($command[0])))) {
                                echo "Command " . $command[0] . "not found!\n";
                            }
                        }
                    }
                }
            } else if (checkCtype($command)) {
                if (!$this->searchCommand(trim(strtolower($command)))) {
                    echo "Command " . $command . " not found!\n";
                }
            } else if ($command !== '') {
                echo "Command " . $command . " not found!\n";
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

    protected function showHelp($commands, $cmdPath = null)
    {
        if ($cmdPath) {
            $cmdPath = implode(' ', $cmdPath) . ' ';
        } else {
            $cmdPath = '';
        }

        $availableCommands = [];

        foreach ($commands as $command) {
            if ($this->whereAt === $command['availableAt']) {
                array_push($availableCommands, [$cmdPath . $command['command'], $command['description']]);
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
        $commandArr = explode(' ', $command);

        if (count($commandArr) === 1 &&
            isset($this->commands[$commandArr[0]])
        ) {
            if (isset($this->commands[$commandArr[0]]['childs']) &&
                is_array($this->commands[$commandArr[0]]['childs']) &&
                count($this->commands[$commandArr[0]]['childs']) > 0
            ) {
                return true;
            }

            if ($this->commands[$commandArr[0]]['availableAt'] === $this->whereAt) {
                return $this->execCommand($this->commands[$commandArr[0]]);
            }
        } else if (count($commandArr) > 1) {
            $commands = $this->commands;

            foreach ($commandArr as $commandKey) {
                if (isset($commands[$commandKey]['childs']) &&
                    is_array($commands[$commandKey]['childs']) &&
                    count($commands[$commandKey]['childs']) > 0
                ) {
                    if (isset($commands[$commandKey]) &&
                        $commandKey === $this->helper->last($commandArr)
                    ) {
                        if ($commands[$commandKey]['availableAt'] === $this->whereAt) {
                            return $this->execCommand($commands[$commandKey]);
                        }
                    }

                    $commands = $commands[$commandKey]['childs'];
                }
            }

            if (isset($commands[$this->helper->last($commandArr)])) {
                if ($commands[$this->helper->last($commandArr)]['availableAt'] === $this->whereAt) {
                    return $this->execCommand($commands[$this->helper->last($commandArr)]);
                }
            }
        }

        return false;
    }

    protected function execCommand($commandArr)
    {
        if (!isset($commandArr['class'])) {
            return false;
        }

        $class = new $commandArr['class'];

        $response = $class->init($this)->{$commandArr['function']}($commandArr['args']);

        if ($response !== null) {
            if ($class->packagesData->responseCode == 0) {
                $color = "%G";
            } else {
                $color = "%R";
            }

            \cli\line("");
            \cli\line($color . $class->packagesData->responseMessage);
            \cli\out("%W");
            if ($class->packagesData->responseData && count($class->packagesData->responseData) > 0) {

                $responseData = true_flatten($class->packagesData->responseData);

                foreach ($responseData as $key => $value) {
                    if ($value === null || $value === '') {
                        $value = 'null';
                    }
                    \cli\line("%b$key : %W$value");
                }
            }
            \cli\line("");

            return true;
        }

        return false;
    }

    protected function setBanner($banner = null)
    {
        $this->banner =
            "%RWelcome to SP!\n\n" .
            "Type help or ? (question mark) for help at any time\n\n" .
            "Enter command and ? (question mark) for specific command help/options\n%W";
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