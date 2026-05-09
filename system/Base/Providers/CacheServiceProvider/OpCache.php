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

    public function getCache($key, $directory = null, $domain = null)
    {
        $this->setDirectory($directory, $domain);

        try {
            include base_path($this->storagePath . $this->directory . '/' . $key . '.php');
        } catch (\throwable $e) {
            return false;
        }

        return ${"value_".$key} ?? false;
    }

    public function setCache($key, $value, $directory = null, $domain = null)
    {
        $this->setDirectory($directory, $domain);

        $value = var_export($value, true);

        $value = str_replace('stdClass::__set_state', '(object)', $value);

        file_put_contents(base_path($this->storagePath . $this->directory . '/' . $key . '.php'), '<?php $value_' . $key . ' = ' . $value . ';', LOCK_EX);

        return $this->getCache($key, $directory);
    }

    public function removeCache($key = null, $directory = null, $domain = null)
    {
        $this->setDirectory($directory, $domain);

        if ($key) {
            if ($this->checkCache($key, $directory)) {
                if (!opcache_invalidate(base_path($this->storagePath . $this->directory . '/' . $key . '.php'), true)) {
                    return false;
                }
            }

            try {
                if (!unlink(base_path($this->storagePath . $this->directory . '/' . $key . '.php'))) {
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
                    $dirContent = scanAllDir(base_path($this->storagePath . $this->directory . '/'));
                } else {
                    $dirContent = scanAllDir(base_path($this->storagePath));
                }

                deleteFilesFolders($dirContent['files']);
                deleteFilesFolders($dirContent['dirs']);
            } catch (\throwable $e) {
                return false;
            }

            return true;
        }
    }

    public function resetCache($key = null, $value = null, $directory = null, $domain = null)
    {
        $this->setDirectory($directory, $domain);

        if ($key && $value) {
            if ($this->removeCache($key)) {
                return $this->setCache($key, $value);
            }
        } else {
            return $this->removeCache($directory);
        }
    }

    public function checkCache($key, $directory = null, $domain = null)
    {
        $this->setDirectory($directory, $domain);

        return file_exists(base_path($this->storagePath . $this->directory . '/' . $key . '.php'));
    }

    protected function setDirectory($directory = null, $domain = null)
    {
        if ($directory) {
            if ($domain) {
                $this->directory = $directory . '/' . $domain . '/';
            } else {
                $this->directory = $directory;
            }
        }

        $this->checkCachePath($this->directory);
    }

    protected function checkCachePath($directory = null, $domain = null)
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
}