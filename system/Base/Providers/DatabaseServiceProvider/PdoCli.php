<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\Pdo\Mysql;

class PdoCli
{
    protected $dbConfig;

    public function __construct($dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    public function init()
    {
        return new Mysql($this->dbConfig->toArray());
    }
}