<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function routenotfoundAction()
    {
        $this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/Error/'));

        $this->view->pick('notfound');
    }
}