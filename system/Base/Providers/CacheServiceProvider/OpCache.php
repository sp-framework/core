<?php

namespace System\Base\Providers\CacheServiceProvider;

class OpCache
{
    protected $storagePath = 'var/storage/cache/opcache/';

    protected $directory = 'tmp';

    public function __construct()
    {
    }

    public function init()
    {
        $this->checkCachePath();

        //Opcache Configuration

        return $this;
    }

    public function getCache($key, $directory = null)
    {
        $this->setDirectory($directory);

        try {
            include base_path($this->storagePath . $this->directory . '/' . $key);
        } catch (\throwable $e) {
            return false;
        }

        return isset($value) ? $value : false;
    }

    public function setCache($key, $value, $directory = null)
    {
        $this->setDirectory($directory);

        $value = var_export($value, true);

        $value = str_replace('stdClass::__set_state', '(object)', $value);

        file_put_contents(base_path($this->storagePath . $this->directory . '/' . $key), '<?php $value = ' . $value . ';', LOCK_EX);

        $this->getCache($key);
    }

    public function removeCache($key = null, $directory = null)
    {
        $this->setDirectory($directory);

        if ($key) {
            if (opcache_is_script_cached(base_path($this->storagePath . $this->directory . '/' . $key))) {
                if (!opcache_invalidate(base_path($this->storagePath . $this->directory . '/' . $key), true)) {
                    return false;
                }
            }

            try {
                if (!unlink(base_path($this->storagePath . $this->directory . '/' . $key))) {
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
                if ($directory) {
                    $files = scanAllDir(base_path($this->storagePath . $this->directory . '/'));
                } else {
                    $files = scanAllDir(base_path($this->storagePath));
                }

                foreach ($files as $file) {
                    deleteFiles(base_path($this->storagePath . $this->directory . '/' . $file));
                }
            } catch (\throwable $e) {
                return false;
            }

            return true;
        }
    }

    public function resetCache($key = null, $value = null, $directory = null)
    {
        $this->setDirectory($directory);

        if ($key && $value) {
            if ($this->removeCache($key)) {
                return $this->setCache($key, $value);
            }
        } else {
            return $this->removeCache($directory);
        }
    }

    protected function checkCachePath($directory = null)
    {
        if ($directory) {
            $path = $this->storagePath . $directory;
        } else {
            $path = $this->storagePath;
        }

        if (!is_dir(base_path($path))) {
            if (!mkdir(base_path($path), 0777, true)) {
                return false;
            }
        }

        return true;
    }

    public function setDirectory($directory = null)
    {
        if ($directory) {
            $this->directory = $directory;
        }

        $this->checkCachePath($this->directory);
    }
}