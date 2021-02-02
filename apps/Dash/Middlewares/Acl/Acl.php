<?php

namespace Apps\Dash\Middlewares\Acl;

use Phalcon\Acl\Component;
use Phalcon\Acl\Role;
use Phalcon\Helper\Arr;
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

    protected $accountPermissions;

    protected $rolePermissions;

    protected $found = false;

    public function process()
    {
        $rolesArr = $this->basepackages->roles->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $key => $value) {
            $roles[$value['id']] = $value;
        }

        $this->account = $this->auth->account();

        if (!$this->account) {
            $this->checkAuthAclMiddlewareSequence();
        }

        $this->accountPermissions = Json::decode($this->account['permissions'], true);
        if ($this->account['role_id'] === '1' && count($this->accountPermissions) === 0) {//Systems Administrators
            return;
        }

        $this->checkCachePath();
        $aclFileDir =
            'var/storage/cache/' .
            $this->app['app_type'] . '/' .
            // $this->app['category'] . '/' .
            // $this->app['sub_category'] . '/' .
            $this->app['route'] . '/acls/';

        $this->setControllerAndAction();

        if ($this->account && $this->account['override_role'] === '1') {
            $this->accountEmail = str_replace('.', '', str_replace('@', '', $this->account['email']));

            if ($this->localContent->has($aclFileDir . $this->accountEmail . $this->account['id'])) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $this->accountEmail . $this->account['id']));
            } else {

                $this->acl->addRole(
                    new Role($this->accountEmail, 'User Override Role')
                );
                // $permissions = Json::decode($this->account['permissions'], true);

                $this->generateComponentsArr();

                foreach ($this->accountPermissions as $appKey => $app) {
                    foreach ($app as $componentKey => $permission) {
                        if ($this->app['id'] == $appKey) {
                            if ($this->components[$componentKey]['route'] === $this->controllerRoute) {
                                $this->buildAndTestAcl($this->accountEmail, $componentKey, $permission);
                                break 2;
                            }
                        }
                    }
                }

                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $this->accountEmail . $this->account['id'], serialize($this->acl));
                }
            }

            if (!$this->acl->isAllowed($this->accountEmail, $this->controllerRoute, $this->action)) {
                throw new PermissionDeniedException();
            }
        } else {
            $this->role = $roles[$this->account['role_id']];

            $this->roleName = strtolower(str_replace(' ', '', $this->role['name']));

            if ($this->localContent->has($aclFileDir . $this->roleName . $this->role['id'] . $this->controllerRoute . $this->action)) {

                $this->acl = unserialize($this->localContent->read($aclFileDir . $this->roleName . $this->role['id'] . $this->controllerRoute . $this->action));

            } else {
                $this->generateComponentsArr();

                $this->acl->addRole(
                    new Role($this->roleName, $this->role['description'])
                );

                $this->rolePermissions = Json::decode($this->role['permissions'], true);

                foreach ($this->rolePermissions as $appKey => $app) {
                    foreach ($app as $componentKey => $permission) {
                        if ($this->app['id'] == $appKey) {
                            if ($this->components[$componentKey]['route'] === $this->controllerRoute &&
                                Arr::has($this->components[$componentKey]['acls'], $this->action)
                            ) {
                                $this->found = true;
                                $this->buildAndTestAcl($this->roleName, $componentKey, $permission);
                                break 2;
                            }
                        }
                    }
                }

                if ($this->config->cache->enabled) {
                    $this->localContent->put($aclFileDir . $this->roleName . $this->role['id'] . $this->controllerRoute . $this->action, serialize($this->acl));
                }
            }
            // var_dump($this->roleName, $this->controllerRoute, $this->action);
            if ($this->found &&
                !$this->acl->isAllowed($this->roleName, $this->controllerRoute, $this->action)
            ) {
                throw new PermissionDeniedException();
            }
        }
    }

    protected function setControllerAndAction()
    {
        $controllerName = $this->dispatcher->getControllerName();

        $component =
            $this->modules->components->getNamedComponentForApp(
                $controllerName,
                $this->app['id']
            );

        if (!$component) {
            $url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

            if ($this->request->isPost()) {
                unset($url[Arr::lastKey($url)]);
            }

            if (isset($this->domain['exclusive_to_default_app']) &&
                $this->domain['exclusive_to_default_app'] == 1
            ) {
                if ($url[0] === $this->app['route']) {
                    unset($url[0]);
                }
            }

            $componentRoute = implode('/', $url);

            $component =
                $this->modules->components->getRouteComponentForApp(
                    strtolower($componentRoute), $this->app['id']
                );
        }

        $this->controllerRoute = $component['route'];

        $this->action = strtolower(str_replace('Action', '', $this->dispatcher->getActiveMethod()));
    }

    protected function buildAndTestAcl($roleName, $componentKey, $permission, $fullAccess = null)
    {
        $componentRoute = $this->components[$componentKey]['route'];
        $componentDescription = $this->components[$componentKey]['description'];
        $componentAcls = $this->components[$componentKey]['acls'];
        // var_dump($this->components[$componentKey]);
        $this->acl->addComponent(
            new Component($componentRoute, $componentDescription), $componentAcls
        );

        // var_dump($permission[$this->action]);
        if ($permission[$this->action] === 1) {
            $this->acl->allow($roleName, $componentRoute, $this->action);
            $this->logger->log->debug(
                'User ' . $this->accountEmail . ' granted access to component ' . $componentRoute . ' for action ' . $this->action
            );
        } else {
            $this->logger->log->debug(
                'User ' . $this->accountEmail . ' denied access to component ' . $componentRoute . ' for action ' . $this->action
            );
        }
    }

    protected function checkCachePath()
    {
        if (
            !is_dir(
                base_path(
                    'var/storage/cache/' .
                    $this->app['app_type'] . '/' .
                    // $this->app['category'] . '/' .
                    // $this->app['sub_category'] . '/' .
                    $this->app['route'] . '/acls/'
                )
            )
        ) {
            if (
                !mkdir(
                    base_path(
                        'var/storage/cache/' .
                        $this->app['app_type'] . '/' .
                        // $this->app['category'] . '/' .
                        // $this->app['sub_category'] . '/' .
                        $this->app['route'] . '/acls/'
                    ), 0777, true
                )
            ) {
                return false;
            }
        }
        return true;
    }

    protected function generateComponentsArr()
    {
        $componentsArr = $this->modules->components->components;

        foreach ($componentsArr as $component) {
            if ($component['class'] && $component['class'] !== '') {
                $reflector = $this->annotations->get($component['class']);
                $methods = $reflector->getMethodsAnnotations();

                if ($methods) {
                    $this->components[$component['id']]['name'] = strtolower($component['name']);
                    $this->components[$component['id']]['route'] = strtolower($component['route']);
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
        $appId = $this->apps->getAppInfo()['id'];

        $acl = $this->modules->middlewares->getNamedMiddlewareForApp('Acl', $appId);
        $aclSequence = (int) $acl['apps'][$appId]['sequence'];

        $auth = $this->modules->middlewares->getNamedMiddlewareForApp('Auth', $appId);
        $authSequence = (int) $auth['apps'][$appId]['sequence'];

        if ($aclSequence < $authSequence) {
            $acl['apps'][$appId]['sequence'] = 99;
            $acl['apps'] = Json::encode($acl['apps']);
            $this->modules->middlewares->update($acl);

            throw new \Exception('ACL middleware sequence is lower then Auth middleware sequence, which is wrong. You need to authenticate before we can apply ACL. I have fixed the problem by changing the ACL middleware sequence to 99.');
        }
    }
}