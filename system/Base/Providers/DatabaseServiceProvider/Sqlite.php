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

    public function init($file = null)
    {
        if (!$file) {
            $this->databaseDir = base_path('.sql/');

            $this->checkDatabasePath();

            $file = $this->databaseDir . 'sp.sqlite';
        }

        return new PhalconSqlite(
            [
                'dbname'    => $file
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