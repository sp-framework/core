<?php

namespace System\Base\Providers\CacheServiceProvider;

class OpCache
{
    protected $cache;

    protected $storagePath = 'var/storage/cache/opcache/';

    public function __construct()
    {
    }

    public function init()
    {
        $this->checkCachePath();

        //Opcache Configuration

        return $this;
    }

    public function getCache($key)
    {
        try {
            include base_path($this->storagePath . $key);
        } catch (\throwable $e) {
            return false;
        }

        return isset($value) ? $value : false;
    }

    public function setCache($key, $value)
    {
        $value = var_export($value, true);

        $value = str_replace('stdClass::__set_state', '(object)', $value);

        file_put_contents(base_path($this->storagePath . $key), '<?php $value = ' . $value . ';', LOCK_EX);

        $this->getCache($key);
    }

    public function removeCache($key = null)
    {
        if ($key) {
            if (opcache_is_script_cached(base_path($this->storagePath . $key))) {
                if (!opcache_invalidate(base_path($this->storagePath . $key), true)) {
                    return false;
                }
            }

            try {
                if (!unlink(base_path($this->storagePath . $key))) {
                    return false;
                }
            } catch (\throwable $e) {
                return false;
            }

            return true;
        } else {
            if (!opcache_reset()) {
                return false;
            }

            try {
                $files = array_diff(scandir(base_path($this->storagePath)), array('..', '.'));

                foreach ($files as $file) {
                    if (!unlink(base_path($this->storagePath . $file))) {
                        return false;
                    }
                }
            } catch (\throwable $e) {
                var_dump($e);
                return false;
            }

            return true;
        }
    }

    public function resetCache($key = null, $value = null)
    {
        if ($key && $value) {
            if ($this->removeCache($key)) {
                return $this->setCache($key, $value);
            }
        } else {
            return $this->removeCache();
        }
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path($this->storagePath))) {
            if (!mkdir(base_path($this->storagePath), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}