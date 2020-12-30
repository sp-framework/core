<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function routeNotFoundAction()
    {
        $this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View'));

        $this->view->pick('errors/routeNotFound');
    }

    public function controllerNotFoundAction()
    {
        $this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/'));

        $this->view->pick('errors/controllerNotFound');
    }

    public function actionNotFoundAction()
    {
        $this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View'));

        $this->view->pick('errors/actionNotFound');
    }
}