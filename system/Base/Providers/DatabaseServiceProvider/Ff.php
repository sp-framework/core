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

    public function __construct($cacheConfig)
    {
        $this->config['auto_cache'] = $cacheConfig->enabled;

        $this->config['cache_lifetime'] = $cacheConfig->timeout;
    }

    public function init()
    {
        $this->databaseDir = base_path('.ff/');

        $this->checkDatabasePath();

        return $this;
    }

    public function store($file, $config = [], $schema = [])
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