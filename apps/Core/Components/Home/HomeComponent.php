<?php

namespace Apps\Core\Components\Home;

use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        try {
            if ($this->access->auth->check()) {
                if ($this->app['default_component_users'] == 0) {
                    return;
                }

                $defaultComponent = $this->modules->components->getById($this->app['default_component_users']);
            } else {
                if ($this->app['default_component_guests'] == 0) {
                    return;
                }

                $defaultComponent = $this->modules->components->getById($this->app['default_component_guests']);
            }

            if ($defaultComponent['class'] === get_class($this)) {
                return;
            }

            $controller = $this->helper->last(explode('/', $defaultComponent['route']));
            $routeArr = explode('/', $defaultComponent['route']);
            unset($routeArr[$this->helper->lastKey($routeArr)]);
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