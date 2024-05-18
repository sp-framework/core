<?php

namespace System\Base\Providers\TerminalServiceProvider\Commands\Show;

use System\Base\Providers\TerminalServiceProvider\Commands;

class Account extends Commands
{
    protected $terminal;

    public function init($terminal = null)
    {
        $this->terminal = $terminal;

        return $this;
    }

    public function account($args = [])
    {
        $account = $this->terminal->getAccount();

        unset($account['security']);
        unset($account['identifier']);
        unset($account['role']);
        unset($account['canlogin']);
        unset($account['sessions']);
        unset($account['agents']);
        unset($account['api_clients']);
        unset($account['tunnels']);
        unset($account['profile']['initials_avatar']);

        $this->addResponse('Ok', 0, ['account' => $account]);

        return true;
    }

    public function whoAmI($args = [])
    {
        $this->addResponse('Ok', 0, ['whoAmI' => $this->terminal->getAccount()['profile']['full_name']]);

        return true;
    }
}