<?php

namespace System\Base\Providers\ContentServiceProvider\Local;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Content
{
    public function init(string $path = null, array $visibility = [])
    {
        return new Filesystem(
            new LocalFilesystemAdapter(
                base_path($path),
                null,
                LOCK_EX,
                LocalFilesystemAdapter::SKIP_LINKS
            ),
            $visibility
        );
    }
}