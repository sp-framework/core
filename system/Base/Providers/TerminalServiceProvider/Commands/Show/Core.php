<?php

namespace System\Base\Providers\TerminalServiceProvider\Commands\Show;

use System\Base\Providers\TerminalServiceProvider\Commands;

class Core extends Commands
{
    protected $terminal;

    public function init($terminal = null)
    {
        $this->terminal = $terminal;

        return $this;
    }

    public function run($args = [])
    {
        $core = $this->core->core;

        unset($core['settings']);
        unset($core['id']);

        $this->addResponse('Ok', 0, ['core' => $core]);

        return true;
    }

    public function version($args = [])
    {
        $this->addResponse('Ok', 0, ['version' => $this->core->core['version']]);

        return true;
    }
}