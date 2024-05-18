<?php

namespace Apps\Core\Components\Home;

use Phalcon\Helper\Arr;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        try {
            $defaultComponentId = $this->app['default_component'];

            if ($this->app['default_component'] == 0) {
                $dashboardComponent = $this->modules->components->getComponentByRouteForAppId('dashboards');

                $defaultComponentId = $dashboardComponent['id'];
            }

            $defaultComponent = $this->modules->components->getById($defaultComponentId);
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