<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
    public function beforeExecuteRoute()
    {
        $this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View'));

        parent::beforeExecuteRoute();
    }

    public function controllerNotFoundAction()
    {
        $this->view->pick('errors/controllernotfound');

        $this->addResponse('Component Not Found', 1);
    }

    public function controllerPackageDependencyErrorAction()
    {
        $this->view->pick('errors/controllerdependencyerror');

        $this->addResponse('Component Dependency Error', 1);
    }

    public function controllerViewDependencyErrorAction()
    {
        $this->view->pick('errors/controllerdependencyerror');

        $this->addResponse('Component Dependency Error', 1);
    }

    public function actionNotFoundAction()
    {
        $this->view->pick('errors/actionnotfound');

        $this->addResponse('Component Action Not Found', 1);
    }

    public function templateErrorAction()
    {
        $this->view->pick('errors/templateerror');

        $this->addResponse('Template For Component Not Found', 1);
    }

    public function routeNotFoundAction()
    {
        $this->view->pick('errors/notfound');

        $this->addResponse('Not Found', 1);
    }

    public function idNotFoundAction()
    {
        $this->view->pick('errors/idnotfound');

        $this->addResponse('Id Not Found', 1);
    }

    public function permissionDeniedAction()
    {
        $this->view->pick('errors/permissiondenied');

        $this->addResponse('Permission denied, contact administrator!', 1);
    }

    public function serverErrorAction()
    {
        $this->view->pick('errors/servererror');

        $this->addResponse('Server Error, contact administrator!', 1);
    }

    public function invalidDataAction(...$params)
    {
        $this->view->pick('errors/servererror');

        $this->addResponse('Invalid data provided. Error: ' . $params[0], 1);
    }
}