<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessPsef extends Calls
{
    public $funcDisplayName = 'Process PSEF';

    protected $args;

    protected $raw_args;

    protected $raw_cwd;

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->addResponse('Ok', 0, []);

        $this->addJobResult($this->packagesData, $args);

        $this->updateJobTask(3, $args);
    }

    public function getRawCmd()
    {
        $this->raw_cwd = 'ls -al /';

        return $this->raw_cwd;
    }
}