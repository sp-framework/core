<?php

namespace Apps\Core\Components\Devtools\Test;

use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    public function viewAction()
    {
        $this->addResponse('Testing', 1, ['test' => 'test']);
        return false;
    }

    public function apiViewAction()
    {
        $this->addResponse('Testing', 1, ['test' => 'test']);
    }

    public function addAction()
    {
        //
    }

    public function updateAction()
    {
        //
    }

    public function removeAction()
    {
        //
    }
}