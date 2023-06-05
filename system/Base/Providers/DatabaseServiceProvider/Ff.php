<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use System\Base\Providers\DatabaseServiceProvider\Ff\Query;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class Ff
{
    protected $ff;

    protected $databaseDir;

    protected $config = [];

    protected $store;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->databaseDir = base_path('.ff/');

        $this->checkDatabasePath();

        return $this;
    }

    public function createStore($file, $config = null, $schema = null)
    {
        return $this->store($file, $config, $schema);
    }

    public function updateStore($file, $config = null, $schema = null)
    {
        return $this->store($file, $config, $schema);
    }

    public function useStore($file)
    {
        $this->store = new Store($file, $this->databaseDir);

        return $this->store;
    }

    protected function store($file, $config = null, $schema = null)
    {
        if ($config) {
            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->store = new Store($file, $this->databaseDir, $this->config, $schema);

        return $this->store;
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