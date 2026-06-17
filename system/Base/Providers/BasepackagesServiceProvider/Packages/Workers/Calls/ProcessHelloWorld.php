<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessHelloWorld extends Calls
{
    public $funcDisplayName = 'Process Hello World!';

    protected $args;

    protected $raw_args;

    protected $raw_cwd;

    public function getRawCmd()
    {
        return $this->raw_cwd = 'echo "Hello World!"';
    }
}