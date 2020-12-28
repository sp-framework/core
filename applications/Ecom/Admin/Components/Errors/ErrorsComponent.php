<?php

namespace Applications\Ecom\Admin\Components\Errors;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function controllerNotFoundAction()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        $this->view->pick('common/errors/notfound');
    }

    public function actionNotFoundAction()
    {
        $this->view->setViewsDir($this->modules->views->getPhalconViewPath());

        $this->view->pick('common/errors/notfound');
    }
}