<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersRoles;

class Roles extends BasePackage
{
    protected $modelToUse = BasepackagesUsersRoles::class;

    protected $packageName = 'roles';

    public $roles;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function addRole(array $data)
    {
        $data = $this->removeMS($data);

        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['name'] . ' role');
        } else {
            $this->addResponse('Error adding new role.', 1);
        }
    }

    public function updateRole(array $data)
    {
        if (!$this->checkForSystemRole($data['id'])) {
            $this->addResponse('Cannot update system role.', 1);

            return false;
        }

        $data = $this->removeMS($data);

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' role');
        } else {
            $this->addResponse('Error updating role.', 1);
        }
    }

    protected function removeMS($data)
    {
        if (!isset($data['id']) ||
            (isset($data['id']) && $data['id'] != '1')
        ) {
            if (isset($data['permissions']) && $data['permissions'] !== '') {
                $data['permissions'] = $this->helper->decode($data['permissions'], true);

                foreach ($data['permissions'] as $app => &$components) {
                    if (is_array($components) && count($components) > 0) {
                        foreach ($components as &$component) {
                            if (isset($component['msview'])) {
                                $component['msview'] = 0;
                            }
                            if (isset($component['msupdate'])) {
                                $component['msupdate'] = 0;
                            }
                        }
                    }
                }
                $data['permissions'] = $this->helper->encode($data['permissions']);
            }
        }

        return $data;
    }

    public function removeRole(array $data)
    {
        if (!$this->checkForSystemRole($data['id'])) {
            $this->addResponse('Cannot remove system role.', 1);

            return false;
        }

        if (isset($data['id'])) {
            $hasAccounts = false;

            if ($this->config->databasetype === 'db') {
                $roleObj = $this->getFirst('id', $data['id']);

                if ($roleObj->getAccounts() && $roleObj->getAccounts()->count() > 0) {
                    $hasAccounts = true;
                }
            } else {
                $this->setFFRelations(true);

                $role = $this->getById($data['id']);

                if (isset($role['accounts']) && is_array($role['accounts']) && count($role['accounts']) > 0) {
                    $hasAccounts = true;
                }
            }

            if ($hasAccounts) {
                $this->addResponse('Role has accounts assigned to it. Cannot removes role.', 1);

                return false;
            }

            if ($this->remove($data['id'], true, false)) {
                $this->addResponse('Removed role');
            } else {
                $this->addResponse('Error removing role.', 1);
            }
        } else {
            $this->addResponse('Error removing role.', 1);
        }
    }

    protected function checkForSystemRole(int $rid)
    {
        $role = $this->getById($rid);

        if ($role) {
            if ($role['type'] == 0) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function generateViewData(int $rid = null)
    {
        $acls = [];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

            if (count($componentsArr) > 0) {
                $components[strtolower($app['id'])] =
                    [
                        'title' => strtoupper($app['name']),
                        'id' => strtoupper($app['id'])
                    ];
                foreach ($componentsArr as $key => $component) {
                    $reflector = $this->annotations->get($component['class']);
                    $methods = $reflector->getMethodsAnnotations();

                    if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
                        $components[strtolower($app['id'])]['childs'][$key]['id'] = $component['id'];
                        $components[strtolower($app['id'])]['childs'][$key]['title'] = $component['name'];
                    }
                }
            }
        }

        $this->packagesData->components = $components;

        $rolesArr = $this->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $roleKey => $roleValue) {
            $roles[$roleValue['id']] =
                [
                    'id'    => $roleValue['id'],
                    'name'  => $roleValue['name']
                ];
        }

        if ($rid) {
            $role = $this->getById($rid);

            if ($role) {
                if ($role['permissions'] && is_string($role['permissions']) && $role['permissions'] !== '') {
                    $permissionsArr = $this->helper->decode($role['permissions'], true);
                } else if ($role['permissions'] && is_array($role['permissions'])) {
                    $permissionsArr = $role['permissions'];
                } else {
                    $permissionsArr = [];
                }

                $permissions = [];

                foreach ($appsArr as $appKey => $app) {
                    $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                    foreach ($componentsArr as $key => $component) {
                        if ($component['class'] && $component['class'] !== '') {
                            $reflector = $this->annotations->get($component['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
                                foreach ($methods as $annotation) {
                                    if ($annotation->getAll('acl')) {
                                        $action = $annotation->getAll('acl')[0]->getArguments();
                                        if ($rid && $rid != 1 &&
                                            ($action['name'] === 'msview' || $action['name'] === 'msupdate')
                                        ) {
                                            continue;
                                        }
                                        $acls[$action['name']] = $action['name'];
                                        if (isset($permissionsArr[$app['id']][$component['id']])) {
                                            $permissions[$app['id']][$component['id']] = $permissionsArr[$app['id']][$component['id']];
                                        } else {
                                            $permissions[$app['id']][$component['id']][$action['name']] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->packagesData->acls = $this->helper->encode($acls);

                $role['permissions'] = $this->helper->encode($permissions);

                $this->packagesData->role = $role;
            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Role Not Found!';

                return;
            }
        } else {
            $role = [];
            $permissions = [];

            foreach ($appsArr as $appKey => $app) {
                $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                foreach ($componentsArr as $key => $component) {
                    //Build ACL Columns
                    if ($component['class'] && $component['class'] !== '') {
                        $reflector = $this->annotations->get($component['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
                            foreach ($methods as $annotation) {
                                if ($annotation->getAll('acl')) {
                                    $action = $annotation->getAll('acl')[0]->getArguments();
                                    $acls[$action['name']] = $action['name'];
                                    $permissions[$app['id']][$component['id']][$action['name']] = 0;
                                }
                            }
                        }
                    }
                }
            }

            $this->packagesData->acls = $this->helper->encode($acls);
            $role['permissions'] = $this->helper->encode($permissions);
            $this->packagesData->role = $role;
        }

        $this->packagesData->apps = $appsArr;

        $this->packagesData->roles = $roles;

        return true;
    }

    public function searchRole(string $roleQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'name LIKE :aName:',
                    'bind'          => [
                        'aName'     => '%' . $roleQueryString . '%'
                    ]
                ];
        } else {
            $conditions = ['name', 'LIKE', '%' . $roleQueryString . '%'];
        }

        $searchRoles = $this->getByParams($conditions);

        if (count($searchRoles) > 0) {
            $roles = [];

            foreach ($searchRoles as $roleKey => $roleValue) {
                $roles[$roleKey]['id'] = $roleValue['id'];
                $roles[$roleKey]['name'] = $roleValue['name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->roles = $roles;

            return true;
        }
    }
}