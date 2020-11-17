<?php

namespace Applications\Admin\Middlewares\Acl;

use Phalcon\Acl\Component;
use Phalcon\Acl\Role;
use Phalcon\Helper\Json;
use System\Base\BaseMiddleware;
use System\Base\Providers\AccessServiceProvider\PermissionDeniedException;

class Acl extends BaseMiddleware
{
    public function process()
    {
        if ($this->auth->hasUserInSession()) {
            $this->checkCachePath();

            $aclFileDir = 'var/storage/cache/acls/';

            $controller = $this->dispatcher->getControllerName();
            $action = str_replace('Action', '', $this->dispatcher->getActiveMethod());

            $rolesArr = $this->roles->getAll()->roles;

            $user = $this->auth->user();

            if ($user && $user['override_role'] === '1') {
                $userEmail = str_replace('.', '', str_replace('@', '', $user['email']));

                if ($this->localContent->has($aclFileDir . $userEmail . $user['id'])) {

                    $this->acl = unserialize($this->localContent->read($aclFileDir . $userEmail . $user['id']));

                } else {

                    $this->acl->addRole(
                        new Role($userEmail, 'User Override Role')
                    );

                    $permissions = Json::decode($user['permissions'], true)['permissions'];

                    $componentsArr = $this->modules->components->components;

                    $components = [];

                    foreach ($componentsArr as $key => $value) {
                        if ($value['type'] === 'crud' || $value['type'] === 'listing') {
                            $components[$value['id']]['name'] = strtolower($value['name']);
                            $components[$value['id']]['type'] = $value['type'];
                            $components[$value['id']]['description'] = $value['description'];
                        }
                    }

                    foreach ($permissions as $componentKey => $permission) {
                        $this->buildAndTestAcl($userEmail, $components, $componentKey, $permission);
                    }
                    $this->localContent->put($aclFileDir . $userEmail . $user['id'], serialize($this->acl));
                }

                if (!$this->acl->isAllowed($userEmail, $controller, $action)) {
                    throw new PermissionDeniedException();
                }
            } else {
                $role = $rolesArr[$user['role_id']];

                $roleName = strtolower(str_replace(' ', '', $role['name']));

                if ($this->localContent->has($aclFileDir . $roleName . $role['id'])) {

                    $this->acl = unserialize($this->localContent->read($aclFileDir . $roleName . $role['id']));

                } else {
                    $componentsArr = $this->modules->components->components;

                    $components = [];

                    foreach ($componentsArr as $key => $value) {
                        if ($value['type'] === 'crud' || $value['type'] === 'listing') {
                            $components[$value['id']]['name'] = strtolower($value['name']);
                            $components[$value['id']]['type'] = $value['type'];
                            $components[$value['id']]['description'] = $value['description'];
                        }
                    }
                    $this->acl->addRole(
                        new Role($roleName, $role['description'])
                    );

                    $permissions = Json::decode($role['permissions'], true);

                    foreach ($permissions as $componentKey => $permission) {
                        $this->buildAndTestAcl($roleName, $components, $componentKey, $permission);
                    }

                    $this->localContent->put($aclFileDir . $roleName . $role['id'], serialize($this->acl));
                }

                if (!$this->acl->isAllowed($roleName, $controller, $action)) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }

    protected function buildAndTestAcl($name, $components, $componentKey, $permission)
    {
        $componentName = $components[$componentKey]['name'];
        $componentDescription = $components[$componentKey]['description'];

        if ($components[$componentKey]['type'] === 'listing') {
            $this->acl->addComponent(
                new Component($componentName, $componentDescription),
                [
                    'view'
                ]
            );

            if ($permission[0] === '1') {
                $this->acl->allow($name, $componentName, 'view');
            } else if ($permission[0] === '0') {
                $this->acl->deny($name, $componentName, 'view');
            }

        } else if ($components[$componentKey]['type'] === 'crud') {
            $this->acl->addComponent(
                new Component($componentName, $componentDescription),
                [
                    'view',
                    'add',
                    'update',
                    'remove'
                ]
            );

            if ($permission[0] === '1') {
                $this->acl->allow($name, $componentName, 'view');
            } else if ($permission[0] === '0') {
                $this->acl->deny($name, $componentName, 'view');
            }
            if ($permission[1] === '1') {
                $this->acl->allow($name, $componentName, 'add');
            } else if ($permission[1] === '0') {
                $this->acl->deny($name, $componentName, 'add');
            }
            if ($permission[2] === '1') {
                $this->acl->allow($name, $componentName, 'update');
            } else if ($permission[2] === '0') {
                $this->acl->deny($name, $componentName, 'update');
            }
            if ($permission[3] === '1') {
                $this->acl->allow($name, $componentName, 'remove');
            } else if ($permission[3] === '0') {
                $this->acl->deny($name, $componentName, 'remove');
            }
        }

        $this->acl->addComponent(
            new Component('home', 'home'),
            [
                'view'
            ]
        );
        $this->acl->allow('*', 'home', '*');

        $this->acl->addComponent(
            new Component('login', 'login'),
            [
                'signin',
                'signout'
            ]
        );
        $this->acl->allow('*', 'login', ['signin', 'signout']);
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
}