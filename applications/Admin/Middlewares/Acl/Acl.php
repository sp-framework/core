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

        if (!$this->auth->hasUserInSession()) {
            return $this->response->redirect('/' . $appName . '/auth');
        }
        $rolesArr = $this->roles->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $key => $value) {
            $roles[$value['id']] = $value;
        }

        $user = $this->auth->user();

        if ($user['role_id'] === '1') {//Systems Administrators
            return;
        }

        $this->checkCachePath();

        $aclFileDir = 'var/storage/cache/acls/';

        $controller = $this->dispatcher->getControllerName();
        $action = str_replace('Action', '', $this->dispatcher->getActiveMethod());


        if ($user && $user['override_role'] === '1') {
            $userEmail = str_replace('.', '', str_replace('@', '', $user['email']));

            if ($this->localContent->has($aclFileDir . $userEmail . $user['id'])) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $userEmail . $user['id']));

            } else {

                $this->acl->addRole(
                    new Role($userEmail, 'User Override Role')
                );

                $permissions = Json::decode($user['permissions'], true)['permissions'];

                $this->generateComponentsArr();

                foreach ($permissions as $componentKey => $permission) {
                    $this->buildAndTestAcl($userEmail, $componentKey, $permission);
                }

                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $userEmail . $user['id'], serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($userEmail, $controller, $action)) {
                throw new PermissionDeniedException();
            }
        } else {
            $role = $roles[$user['role_id']];

            $roleName = strtolower(str_replace(' ', '', $role['name']));

            if ($this->localContent->has($aclFileDir . $roleName . $role['id'])) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $roleName . $role['id']));

            } else {
                $this->generateComponentsArr();

                $this->acl->addRole(
                    new Role($roleName, $role['description'])
                );

                $permissions = Json::decode($role['permissions'], true);

                // if ($user['role_id'] === '1') {
                //     $this->buildAndTestAcl($roleName, $componentKey, $permission, true);//System Admins
                // } else {
                    foreach ($permissions as $componentKey => $permission) {
                        $this->buildAndTestAcl($roleName, $componentKey, $permission);
                    }
                // }
                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $roleName . $role['id'], serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($roleName, $controller, $action)) {
                throw new PermissionDeniedException();
            }
        }
    }

    protected function buildAndTestAcl($name, $componentKey, $permission, $fullAccess = null)
    {
        $componentName = $this->components[$componentKey]['name'];
        $componentDescription = $this->components[$componentKey]['description'];
        // var_dump($this->components[$componentKey]['type']);

        if ($this->components[$componentKey]['type'] === 'listing') {
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

        } else if ($this->components[$componentKey]['type'] === 'crud') {
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
        } else if ($this->components[$componentKey]['type'] === 'system') {
            if ($this->components[$componentKey]['class']) {
                var_dump($this->components[$componentKey]['class']);
            }
        }

        $this->acl->addComponent(
            new Component('home', 'home'),
            [
                'view'
            ]
        );
        $this->acl->allow('*', 'home', '*');

        // $this->acl->addComponent(
        //     new Component('auth', 'auth'),
        //     [
        //         'login',
        //         'logout'
        //     ]
        // );
        // $this->acl->allow('*', 'auth', ['login', 'logout']);
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

        foreach ($componentsArr as $key => $value) {
            // if ($value['type'] === 'crud' || $value['type'] === 'listing') {
                $this->components[$value['id']]['name'] = strtolower($value['name']);
                $this->components[$value['id']]['type'] = $value['type'];
                $this->components[$value['id']]['description'] = $value['description'];
            // }
        }
    }
}