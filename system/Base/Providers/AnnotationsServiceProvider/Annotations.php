<?php

namespace System\Base\Providers\AnnotationsServiceProvider;

use Phalcon\Annotations\Adapter\Stream;

class Annotations
{
    protected $annotations;

    public function __construct()
    {
    }

    public function init()
    {
        $this->checkCachePath();

        $cacheOptions = [
            'annotationsDir'       => base_path('var/storage/cache/annotations/')
        ];

        $this->annotations = new Stream($cacheOptions);

        return $this->annotations;
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/annotations/'))) {
            if (!mkdir(base_path('var/storage/cache/annotations/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}