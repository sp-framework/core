<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use SleekDB\Query;
use SleekDB\Store;

class Ff
{
    protected $ff;

    protected $databaseDir;

    protected $config;

    protected $store;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->databaseDir = base_path('.ff/');

        $this->config =
        [
            "auto_cache" => true,
            "cache_lifetime" => null,
            "timeout" => false,
            "primary_key" => "id",
            "search" => [
                "min_length" => 2,
                "mode" => "or",
                "score_key" => "scoreKey",
                "algorithm" => Query::SEARCH_ALGORITHM["hits"]
            ],
            "folder_permissions" => 0777
        ];

        $this->checkDatabasePath();

        return $this;
    }

    public function use($file, $config = null)
    {
        if ($config) {
            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->store = new Store($file, $this->databaseDir, $this->config);

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