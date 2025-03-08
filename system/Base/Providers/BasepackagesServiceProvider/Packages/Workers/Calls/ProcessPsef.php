<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessPsef extends Calls
{
    public $funcDisplayName = 'Process PSEF';

    protected $raw_args;

    protected $raw_cwd;

    public function getRawCmd()
    {
        $this->raw_cwd = 'ls -al /';

        return $this->raw_cwd;
    }
}