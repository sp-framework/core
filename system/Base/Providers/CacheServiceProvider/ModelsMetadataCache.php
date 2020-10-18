<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Mvc\Model\MetaData\Stream;

class ModelsMetadataCache
{
    protected $cache;

    public function __construct()
    {
    }

    public function init()
    {
        $this->checkCachePath();

        $cacheOptions = [
            'metaDataDir'       => base_path('var/storage/cache/metadata/')
        ];

        $this->cache = new Stream($cacheOptions);

        return $this->cache;
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/metadata/'))) {
            if (!mkdir(base_path('var/storage/cache/metadata/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}