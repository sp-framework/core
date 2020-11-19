<?php

namespace Applications\Admin\Middlewares\Acl;

use Phalcon\Acl\Component;
use Phalcon\Acl\Role;
use Phalcon\Helper\Json;
use System\Base\BaseMiddleware;
use System\Base\Providers\AccessServiceProvider\PermissionDeniedException;

class Acl extends BaseMiddleware
{
    protected $components = [];

    protected $controller;

    protected $action;

    protected $user;

    protected $userEmail;

    protected $role;

    public function process()
    {
        $appName = strtolower($this->application['name']);
        $givenRoute = $this->request->getUri();
        $guestAccess =
        [
            '/' . $appName . '/auth',
            '/' . $appName . '/auth/login',
            '/' . $appName . '/auth/logout',
            '/' . $appName . '/auth/forgot',
            '/' . $appName . '/auth/pwreset',
            '/' . $appName . '/auth/pwresetlink',
            '/' . $appName . '/auth/pwresetforgot',
        ];
        if (in_array($givenRoute, $guestAccess)) {
            return;
        }

        if (!$this->auth->hasUserInSession()) {//Not Authenticated
            return $this->response->redirect('/' . $appName . '/auth');
        }

        $rolesArr = $this->roles->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $key => $value) {
            $roles[$value['id']] = $value;
        }

        $this->user = $this->auth->user();

        if ($this->user['role_id'] === '1') {//Systems Administrators
            return;
        }

        $this->checkCachePath();
        $aclFileDir = 'var/storage/cache/acls/';

        $this->controller = $this->dispatcher->getControllerName();
        $this->action = str_replace('Action', '', $this->dispatcher->getActiveMethod());

        if ($this->user && $this->user['override_role'] === '1') {
            $this->userEmail = str_replace('.', '', str_replace('@', '', $this->user['email']));

            if ($this->localContent->has($aclFileDir . $this->userEmail . $this->user['id'])) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $this->userEmail . $this->user['id']));

            } else {

                $this->acl->addRole(
                    new Role($this->userEmail, 'User Override Role')
                );
                $permissions = Json::decode($this->user['permissions'], true);

                $this->generateComponentsArr();

                foreach ($permissions as $componentKey => $permission) {
                    $this->buildAndTestAcl($this->userEmail, $componentKey, $permission);
                }

                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $this->userEmail . $this->user['id'], serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($this->userEmail, $this->controller, $this->action)) {
                throw new PermissionDeniedException();
            }
        } else {
            $this->role = $roles[$this->user['role_id']];

            $this->roleName = strtolower(str_replace(' ', '', $this->role['name']));

            if ($this->localContent->has($aclFileDir . $this->roleName . $this->role['id'] . $this->controller . $this->action)) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $this->roleName . $this->role['id'] . $this->controller . $this->action));

            } else {
                $this->generateComponentsArr();

                $this->acl->addRole(
                    new Role($this->roleName, $this->role['description'])
                );

                $permissions = Json::decode($this->role['permissions'], true);

                foreach ($permissions as $componentKey => $permission) {
                    if ($this->components[$componentKey]['name'] === $this->controller) {
                        $this->buildAndTestAcl($this->roleName, $componentKey, $permission);
                        break;
                    }
                }
                // var_dump($this->acl);
                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $this->roleName . $this->role['id'] . $this->controller . $this->action, serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($this->roleName, $this->controller, $this->action)) {
                throw new PermissionDeniedException();
            }
        }
    }

    protected function buildAndTestAcl($name, $componentKey, $permission, $fullAccess = null)
    {
        $componentName = $this->components[$componentKey]['name'];
        $componentDescription = $this->components[$componentKey]['description'];
        $componentAcls = $this->components[$componentKey]['acls'];
        // var_dump($this->components[$componentKey]);
        $this->acl->addComponent(
            new Component($componentName, $componentDescription), $componentAcls
        );

        // var_dump($permission[$this->action]);
        if ($permission[$this->action] === 1) {
            $this->acl->allow($name, $componentName, $this->action);
            $this->logger->log->debug('User ' . $this->userEmail . ' granted access to component ' . $componentName . ' for action ' . $this->action);
        } else {
            $this->logger->log->debug('User ' . $this->userEmail . ' denied access to component ' . $componentName . ' for action ' . $this->action);
        }
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/acls/'))) {
            if (!mkdir(base_path('var/storage/cache/acls/'), 0777, true)) {
                return false;
            }
        }
        return true;
    }

    protected function generateComponentsArr()
    {
        $componentsArr = $this->modules->components->components;

        // $components = [];

        foreach ($componentsArr as $component) {
            if ($component['class'] && $component['class'] !== '') {
                $reflector = $this->annotations->get($component['class']);
                $methods = $reflector->getMethodsAnnotations();

                if ($methods) {
                    $this->components[$component['id']]['name'] = strtolower($component['name']);
                    $this->components[$component['id']]['description'] = $component['description'];
                    foreach ($methods as $annotation) {
                        $action = $annotation->getAll('acl')[0]->getArguments();
                        $acls[$action['name']] = $action['name'];
                        $this->components[$component['id']]['acls'][$action['name']] = $action['name'];
                    }
                }
            }
        }
    }
}