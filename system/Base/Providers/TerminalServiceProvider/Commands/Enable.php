<?php

namespace System\Base\Providers\TerminalServiceProvider\Commands;

use System\Base\Providers\TerminalServiceProvider\Commands;

class Enable extends Commands
{
    protected $terminal;

    protected $username;

    protected $password;

    public function init($terminal = null)
    {
        $this->terminal = $terminal;

        return $this;
    }

    public function run($args = [], $initial = true)
    {
        if ($initial) {
            \cli\line("%rEnter username and password\n");
            \cli\out("%wUsername: ");
        } else {
            system('stty -echo');
            \cli\out("%wPassword: ");
        }
        $cliHandle = fopen('php://stdin','r');

        $command = rtrim(fgets($cliHandle), "\r\n");

        while (true) {
            if ($command !== '') {
                if ($initial) {
                    $this->username = $command;
                } else {
                    $this->password = $command;
                    system('stty echo');
                }
                if ($this->username && !$this->password) {
                    $this->run([], false);
                } else if (!$this->username && $this->password) {
                    $this->run([]);
                } else {
                    break;
                }
            }

            if ($this->username && $this->password) {
                return $this->performLogin();

                break;
            } else {
                $this->run(true);
            }
        }

        return false;
    }

    protected function performLogin()
    {
        try {
            $login = $this->terminal->auth->attempt(['user' => $this->username, 'pass' => $this->password]);
            if ($login) {
                $this->terminal->setWhereAt('enable');
                $this->terminal->setPrompt('# ');
                $this->terminal->setAccount($this->terminal->auth->account());
                $this->terminal->setLoginAt(time());

                $this->addResponse('Authenticated! Welcome ' . $this->terminal->getAccount()['profile']['full_name'] ?? $this->terminal->getAccount()['email'] . '...');

                return true;
            }

        } catch (\Exception $e) {
            return false;
        }

        $this->addResponse('Login Incorrect! Try again...', 1);

        return false;
    }
}