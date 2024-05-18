<?php

namespace System\Base\Providers\ContentServiceProvider\Remote;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class Dropbox
{
    public function init($authorizationToken = null, $caseSensitive = false)
    {
        if (!$authorizationToken) {
            return $this;
        }

        return new Filesystem(
            new DropboxAdapter(
                new Client($authorizationToken)
            ),
            ['case_sensitive' => $caseSensitive]
        );
    }
}
