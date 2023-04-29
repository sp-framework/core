<?php

namespace Apps\Core\Components\Errors;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function beforeExecuteRoute()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        parent::beforeExecuteRoute();
    }

    public function controllerNotFoundAction()
    {
        $this->view->pick('common/errors/controllernotfound');
    }

    public function actionNotFoundAction()
    {
        $this->view->pick('common/errors/actionnotfound');
    }

    public function routeNotFoundAction()
    {
        $this->view->pick('common/errors/routenotfound');
    }

    public function idNotFoundAction()
    {
        $this->view->pick('common/errors/idnotfound');
    }
}