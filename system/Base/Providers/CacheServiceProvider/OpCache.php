<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Di\DiInterface;

class OpCache
{
    private $container;

    protected $cache;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        if ($this->container->getShared('config')->cache) {
            //https://medium.com/@dylanwenzlau/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad
            //
            //Tried and tested, works great.

            return false;

            function cache_set($key, $val) {
                $val = var_export($val, true);
                $val = str_replace('stdClass::__set_state', '(object)', $val);

                // Write to temp file first to ensure atomicity
                $tmp = "/var/storage/cache/$key." . uniqid('', true) . '.tmp';
                file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
                rename($tmp, "/var/storage/cache/$key");
            }

            function cache_get($key) {
                include "/var/storage/cache/$key";
                return isset($val) ? $val : false;
            }

            $data = array_fill(0, 1000000, ‘hi’); // your application data here
            cache_set('my_key', $data);
            apc_store('my_key', $data);
            $t = microtime(true);
            $data = cache_get('my_key');
            var_dump(microtime(true) - $t);

        } else {
            return false;
        }
    }
}