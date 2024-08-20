<?php

namespace System\Base\Providers\TerminalServiceProvider\Commands;

use System\Base\Providers\TerminalServiceProvider\Commands;

class Enable extends Commands
{
    protected $terminal;

    protected $username;

    protected $password;

    protected $userPromptCount = 0;

    protected $passPromptCount = 0;

    public function init($terminal = null)
    {
        $this->terminal = $terminal;

        return $this;
    }

    public function run($args = [], $initial = true)
    {
        $command = [];

        if ($initial) {
            \cli\line("%r%w");
            \cli\line("%bEnter username and password\n");
            \cli\out("%wUsername: ");
        } else {
            \cli\out("%wPassword: ");
            readline_callback_handler_install("", function () {});
        }

        while (true) {
            $input = stream_get_contents(STDIN, 1);

            if (ord($input) == 10) {
                if (!$initial) {
                    \cli\line("%r%w");
                }
                break;
            } else if (ord($input) == 127) {
                if (count($command) === 0) {
                    continue;
                }
                array_pop($command);
                fwrite(STDOUT, chr(8));
                fwrite(STDOUT, "\033[0K");
            } else {
                $command[] = $input;
                if (!$initial) {
                    fwrite(STDOUT, '*');
                }
            }
        }

        $command = join($command);

        while (true) {
            if ($command !== '') {
                if ($initial) {
                    $this->username = $command;
                } else {
                    $this->password = $command;
                }
                if ($this->username && !$this->password) {
                    $initial = false;
                } else if (!$this->username && $this->password) {
                    $initial = true;
                }
            } else {
                if ($initial) {
                    $this->userPromptCount++;
                } else {
                    $this->passPromptCount++;
                }
            }

            break;
        }

        if ($this->username && $this->password) {
            readline_callback_handler_remove();

            return $this->performLogin();
        }


        if ($this->userPromptCount >= 3 || $this->passPromptCount >= 3) {
            readline_callback_handler_remove();

            $this->addResponse('Login Incorrect! Try again...', 1);

            return true;
        }

        return $this->run($args, $initial);
    }

    protected function performLogin()
    {
        try {
            $login = $this->terminal->access->auth->attempt(['user' => $this->username, 'pass' => $this->password]);
            if ($login) {
                $this->terminal->setWhereAt('enable');
                $this->terminal->setPrompt('# ');
                $this->terminal->setAccount($this->terminal->access->auth->account());
                $this->terminal->setLoginAt(time());

                readline_read_history(base_path('var/terminal/history/' . $this->terminal->getAccount()['id']));

                $this->addResponse('Authenticated! Welcome ' . $this->terminal->getAccount()['profile']['full_name'] ?? $this->terminal->getAccount()['email'] . '...');

                return true;
            }

        } catch (\Exception $e) {
            return false;
        }

        $this->addResponse('Login Incorrect! Try again...', 1);

        return false;
    }

    public function clearHistory()
    {
        if ($this->terminal->getAccount() && $this->terminal->getAccount()['id']) {
            if (file_exists(base_path('var/terminal/history/' . $this->terminal->getAccount()['id']))) {
                unlink(base_path('var/terminal/history/' . $this->terminal->getAccount()['id']));
            }

            readline_clear_history();

            $this->addResponse('Cleared history for ' . $this->terminal->getAccount()['profile']['full_name'] ?? $this->terminal->getAccount()['email']);

            return true;
        }
    }

    public function getCommands()
    {
        return
            [
                [
                    "availableAt"   => "disable",
                    "command"       => "enable",
                    "description"   => "Enter enable mode",
                    "class"         => "\\System\\Base\\Providers\\TerminalServiceProvider\\Commands\\Enable",
                    "function"      => "run",
                    "args"          => []
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "clear history",
                    "description"   => "Clear terminal history",
                    "class"         => "\\System\\Base\\Providers\\TerminalServiceProvider\\Commands\\Enable",
                    "function"      => "clearHistory",
                    "args"          => []
                ]
            ];
    }
}