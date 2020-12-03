<?php

namespace Applications\Ecom\Admin\Packages\Module;

use System\Base\BasePackage;

class TestEmail extends BasePackage
{
    public function runTest($postData)
    {
        if ($this->email->sendTestEmail($postData)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = $this->email->getDebugOutput();

            return true;
        }
    }
}