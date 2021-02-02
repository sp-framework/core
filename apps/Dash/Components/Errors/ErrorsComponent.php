<?php

namespace Apps\Dash\Components\Errors;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function controllerNotFoundAction()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        $this->view->pick('common/errors/controllernotfound');
    }

    public function actionNotFoundAction()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        $this->view->pick('common/errors/actionnotfound');
    }

    public function routeNotFoundAction()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        $this->view->pick('common/errors/routenotfound');
    }
}