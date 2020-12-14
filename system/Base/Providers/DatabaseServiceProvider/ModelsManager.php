<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Mvc\Model\Manager;

class ModelsManager
{
    protected $modelsManager;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->modelsManager = new Manager();

        return $this->modelsManager;
    }
}