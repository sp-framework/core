<?php

namespace Applications\Admin\Components\Errors;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function controllerNotFoundAction()
    {
        $this->view->pick('errors/notfound');
    }

    public function actionNotFoundAction()
    {
        $this->view->pick('errors/notfound');
    }
}