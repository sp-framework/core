<?php

namespace System\Base\Providers\TerminalServiceProvider;

use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToReadFile;
use ReflectionClass;
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

    protected $commands = [];

    protected $autoCompleteList = [];

    protected $helpList = [];

    protected $execCommandsList = [];

    protected $sessionTimeout = 3600;

    protected $loginAt;

    protected $hasChilds = false;

    protected $commandsDir = 'system/Base/Providers/TerminalServiceProvider/Commands/';

    public function init()
    {
        try {
            $this->getAllCommands();

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

    public function run($terminated = false)
    {
        $this->updateAutoComplete();

        if (!pcntl_async_signals()) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, function($signal) {
                switch ($signal) {
                    case SIGINT:
                        $this->run(true);
                }
            });
        }

        if ($terminated) {
            $command = readline();
        } else {
            $command = readline($this->hostname . $this->prompt);
        }

        while (true) {
            if ($command === 'exit') {
                if ($this->whereAt === 'disable') {
                    break;
                } else {
                    if ($this->account && $this->checkHistoryPath()) {
                        readline_write_history(base_path('var/terminal/history/' . $this->account['id']));
                    }

                    $this->account = null;
                    $this->whereAt = 'disable';
                    $this->prompt = '> ';
                }
            } else if (str_contains($command, '?') || $command === '?' || $command === 'help') {
                $this->showHelp();
            } else if (checkCtype($command)) {
                if (!$this->searchCommand(trim(strtolower($command)))) {
                    echo "Command " . trim($command) . " not found!\n";
                } else {
                    readline_add_history($command);
                }
            } else if ($command !== '') {
                echo "Command " . trim($command) . " not found!\n";
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

    protected function showHelp()
    {
        if (count($this->helpList[$this->whereAt]) > 0) {
            $table = new \cli\Table();
            $table->setHeaders(['Available Commands', 'Description']);
            $table->setRows($this->helpList[$this->whereAt]);
            $table->setRenderer(new \cli\table\Ascii([25, 100]));
            $table->display();
        }
    }

    protected function searchCommand($command)
    {
        if (count($this->execCommandsList[$this->whereAt]) > 0) {
            foreach ($this->execCommandsList[$this->whereAt] as $commands) {
                if ($command === $commands['command']) {
                    return $this->execCommand($commands);
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

    protected function updateAutoComplete()
    {
        readline_completion_function(function($input, $index) {
            if ($input !== '') {
                $rl_info = readline_info();
                $full_input = substr($rl_info['line_buffer'], 0, $rl_info['end']);

                $matches = [];

                foreach ($this->autoCompleteList[$this->whereAt] as $list) {
                    if (str_starts_with($list, $full_input)) {
                        $matches[] = substr($list, $index);
                    }
                }
                return $matches;
            }

            return [];
        });
    }

    protected function setBanner($banner = null)
    {
        $this->banner =
            "%BWelcome to SP!\n\n" .
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

    protected function getAllCommands()
    {
        $commandsArr =
            $this->localContent->listContents($this->commandsDir, true)
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();

        $this->commands = [];

        if (count($commandsArr) > 0) {
            foreach ($commandsArr as $key => $command) {
                $command = ucfirst($command);
                $command = str_replace('/', '\\', $command);
                $command = str_replace('.php', '', $command);

                try {
                    $command = new $command();

                    if (method_exists($command, 'getCommands')) {
                        $commandReflection = new ReflectionClass($command);

                        $commandKey = str_replace('\\', '', $commandReflection->getName());

                        $this->commands[$commandKey] = $command->getCommands();
                    }
                } catch (\throwable $e) {
                    if ($this->config->logs->exceptions) {
                        $this->logger->logExceptions->critical(json_trace($e));
                    }
                    continue;
                }
            }
        }

        foreach ($this->commands as $commandClass => $commandsArr) {
            foreach ($commandsArr as $command) {
                if (!isset($this->autoCompleteList[$command['availableAt']])) {
                    $this->autoCompleteList[$command['availableAt']] = [];
                }
                array_push($this->autoCompleteList[$command['availableAt']], $command['command']);

                if (!isset($this->helpList[$command['availableAt']])) {
                    $this->helpList[$command['availableAt']] = [];

                    if ($command['availableAt'] === 'disable') {
                        array_push($this->helpList[$command['availableAt']], ['exit', 'Exit the terminal']);
                    } else {
                        array_push($this->helpList[$command['availableAt']], ['exit', 'Logout']);
                    }
                }

                array_push($this->helpList[$command['availableAt']], [$command['command'], $command['description']]);

                if (!isset($this->execCommandsList[$command['availableAt']])) {
                    $this->execCommandsList[$command['availableAt']] = [];
                }
                array_push($this->execCommandsList[$command['availableAt']], $command);
            }
        }
    }

    protected function checkHistoryPath()
    {
        if (!is_dir(base_path('var/terminal/history/'))) {
            if (!mkdir(base_path('var/terminal/history/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}