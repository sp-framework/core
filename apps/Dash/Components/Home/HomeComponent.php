<?php

namespace Apps\Dash\Components\Home;

use Phalcon\Helper\Arr;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    public function viewAction()
    {
        try {
            $defaultComponent = $this->modules->components->getById($this->app['default_component']);
            $controller = Arr::last(explode('/', $defaultComponent['route']));
            $routeArr = explode('/', $defaultComponent['route']);
            unset($routeArr[Arr::lastKey($routeArr)]);
            $viewPath = join('/', $routeArr);

            $reflection = new \ReflectionClass(($defaultComponent['class']));

            $namespace = $reflection->getNamespaceName();

            $this->view->setViewsDir($this->view->getViewsDir() . $viewPath);

            $this->dispatcher->forward(
                [
                    'controller'    => $controller,
                    'action'        => 'view',
                    'namespace'     => $namespace
                ]
            );
        } catch (\Exception $e) {
            dump($e);die();
            throw $e;
        }
    }

    public function addAction()
    {
        return;
    }

    public function updateAction()
    {
        return;
    }

    public function removeAction()
    {
        return;
    }
}