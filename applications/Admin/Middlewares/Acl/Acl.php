<?php

namespace Applications\Admin\Middlewares\Acl;

use Phalcon\Acl\Component;
use Phalcon\Acl\Role;
use Phalcon\Helper\Json;
use System\Base\BaseMiddleware;
use System\Base\Providers\AccessServiceProvider\Exceptions\PermissionDeniedException;

class Acl extends BaseMiddleware
{
    protected $components = [];

    protected $controller;

    protected $action;

    protected $account;

    protected $accountEmail;

    protected $role;

    public function process()
    {
        $appName = strtolower($this->application['name']);
        $givenRoute = rtrim(explode('/q/', $this->request->getUri())[0], '/');

        $guestAccess =
        [
            '/' . $appName . '/auth',
            '/' . $appName . '/auth/login',
            '/' . $appName . '/auth/logout',
            '/' . $appName . '/auth/forgot',
            '/' . $appName . '/auth/pwreset'
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

        $this->account = $this->auth->account();

        if (!$this->account) {
            $this->checkAuthAclMiddlewareSequence();
        }

        if ($this->account['role_id'] === '1') {//Systems Administrators
            return;
        }

        $this->checkCachePath();
        $aclFileDir = 'var/storage/cache/acls/';

        $this->controller = $this->dispatcher->getControllerName();
        $this->action = str_replace('Action', '', $this->dispatcher->getActiveMethod());

        if ($this->account && $this->account['override_role'] === '1') {
            $this->accountEmail = str_replace('.', '', str_replace('@', '', $this->account['email']));

            if ($this->localContent->has($aclFileDir . $this->accountEmail . $this->account['id'])) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $this->accountEmail . $this->account['id']));

            } else {

                $this->acl->addRole(
                    new Role($this->accountEmail, 'User Override Role')
                );
                $permissions = Json::decode($this->account['permissions'], true);

                $this->generateComponentsArr();

                foreach ($permissions as $componentKey => $permission) {
                    $this->buildAndTestAcl($this->accountEmail, $componentKey, $permission);
                }

                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $this->accountEmail . $this->account['id'], serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($this->accountEmail, $this->controller, $this->action)) {
                throw new PermissionDeniedException();
            }
        } else {
            $this->role = $roles[$this->account['role_id']];

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
            $this->logger->log->debug('User ' . $this->accountEmail . ' granted access to component ' . $componentName . ' for action ' . $this->action);
        } else {
            $this->logger->log->debug('User ' . $this->accountEmail . ' denied access to component ' . $componentName . ' for action ' . $this->action);
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

    protected function checkAuthAclMiddlewareSequence()
    {
        $acl = $this->modules->middlewares->getNamedMiddlewareForApplication('Acl', $this->modules->applications->getApplicationInfo()['id']);
        $aclSequence = (int) $acl['sequence'];

        $authSequence = (int) $this->modules->middlewares->getNamedMiddlewareForApplication('Auth', $this->modules->applications->getApplicationInfo()['id'])['sequence'];

        if ($aclSequence < $authSequence) {
            $acl['sequence'] = 99;
            $this->modules->middlewares->update($acl);

            throw new \Exception('ACL middleware sequence is lower then Auth middleware sequence, which is wrong. You need to authenticate before we can apply ACL. I have fixed the problem by changing the ACL middleware sequence to 99.');
        }
    }
}