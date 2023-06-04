<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\Pdo\Sqlite as PhalconSqlite;

class Sqlite
{
    protected $databaseDir;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->databaseDir = base_path('.sql/');

        $this->checkDatabasePath();

        return new PhalconSqlite(
            [
                'dbname'    => $this->databaseDir . 'baz.sqlite'
            ]
        );

    }

    protected function checkDatabasePath()
    {
        if (!is_dir($this->databaseDir)) {
            if (!mkdir($this->databaseDir, 0777, true)) {
                return false;
            }
        }

        return true;
    }
}