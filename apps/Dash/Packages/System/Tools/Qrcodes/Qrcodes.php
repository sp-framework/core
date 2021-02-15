<?php

namespace Apps\Dash\Packages\System\Tools\Qrcodes;

use System\Base\BasePackage;

class Qrcodes extends BasePackage
{
    protected $qrcodes;

    protected $qrcodesSettings;

    public function init()
    {
        include (__DIR__ . '/vendor/autoload.php');

        //

        return $this;
    }
}