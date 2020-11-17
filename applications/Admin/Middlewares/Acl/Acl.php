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
        $controller = $this->dispatcher->getControllerName();
        $action = str_replace('Action', '', $this->dispatcher->getActiveMethod());

        $rolesArr = $this->roles->getAll()->roles;

        $componentsArr = $this->modules->components->components;

        $components = [];

        foreach ($componentsArr as $key => $value) {
            if ($value['type'] === 'crud' || $value['type'] === 'listing') {
                $components[$value['id']]['name'] = strtolower($value['name']);
                $components[$value['id']]['type'] = $value['type'];
                $components[$value['id']]['description'] = $value['description'];
            }
        }

        $user = $this->auth->user();

        if ($user['override_role'] === '0') {
            foreach ($rolesArr as $roleKey => $role) {
                if ($role['id'] === $user['role_id']) {
                    $roleName = strtolower(str_replace(' ', '', $role['name']));
                    $this->acl->addRole(
                        new Role($roleName, $role['description'])
                    );

                    $permissions = Json::decode($role['permissions'], true);

                    foreach ($permissions as $componentKey => $permission) {

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
                                $this->acl->allow($roleName, $componentName, 'view');
                            } else if ($permission[0] === '0') {
                                $this->acl->deny($roleName, $componentName, 'view');
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
                                $this->acl->allow($roleName, $componentName, 'view');
                            } else if ($permission[0] === '0') {
                                $this->acl->deny($roleName, $componentName, 'view');
                            }
                            if ($permission[1] === '1') {
                                $this->acl->allow($roleName, $componentName, 'add');
                            } else if ($permission[1] === '0') {
                                $this->acl->deny($roleName, $componentName, 'add');
                            }
                            if ($permission[2] === '1') {
                                $this->acl->allow($roleName, $componentName, 'update');
                            } else if ($permission[2] === '0') {
                                $this->acl->deny($roleName, $componentName, 'update');
                            }
                            if ($permission[3] === '1') {
                                $this->acl->allow($roleName, $componentName, 'remove');
                            } else if ($permission[3] === '0') {
                                $this->acl->deny($roleName, $componentName, 'remove');
                            }
                        }
                    }
                }
            }
            // var_dump($this->acl->isAllowed($roleName, $controller, $action));
            if (!$this->acl->isAllowed($roleName, $controller, $action)) {
                throw new PermissionDeniedException();
            }
        } else {
            $userEmail = str_replace('.', '', str_replace('@', '', $user['email']));

            $this->acl->addRole(
                new Role($userEmail, 'User Override Role')
            );

            $permissions = Json::decode($user['permissions'], true)['permissions'];

            foreach ($permissions as $componentKey => $permission) {

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
                        $this->acl->allow($userEmail, $componentName, 'view');
                    } else if ($permission[0] === '0') {
                        $this->acl->deny($userEmail, $componentName, 'view');
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
                        $this->acl->allow($userEmail, $componentName, 'view');
                    } else if ($permission[0] === '0') {
                        $this->acl->deny($userEmail, $componentName, 'view');
                    }
                    if ($permission[1] === '1') {
                        $this->acl->allow($userEmail, $componentName, 'add');
                    } else if ($permission[1] === '0') {
                        $this->acl->deny($userEmail, $componentName, 'add');
                    }
                    if ($permission[2] === '1') {
                        $this->acl->allow($userEmail, $componentName, 'update');
                    } else if ($permission[2] === '0') {
                        $this->acl->deny($userEmail, $componentName, 'update');
                    }
                    if ($permission[3] === '1') {
                        $this->acl->allow($userEmail, $componentName, 'remove');
                    } else if ($permission[3] === '0') {
                        $this->acl->deny($userEmail, $componentName, 'remove');
                    }
                }
            }
            // var_dump($this->acl->isAllowed($userEmail, $controller, $action));
            if (!$this->acl->isAllowed($userEmail, $controller, $action)) {
                throw new PermissionDeniedException();
            }
        }
    }
}