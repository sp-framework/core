<?php

namespace Applications\Ecom\Admin\Packages\Module;

use System\Base\BasePackage;

class Remove extends BasePackage
{
    protected $postData;

    public function runProcess($postData)
    {
        $this->postData = $postData;
    }
}