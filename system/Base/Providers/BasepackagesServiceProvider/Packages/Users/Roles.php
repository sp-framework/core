<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
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

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addRole(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['name'] . ' role');
        } else {
            $this->addResponse('Error adding new role.', 1);
        }
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function updateRole(array $data)
    {
        if (!$this->checkForSystemRole($data['id'])) {
            $role = $this->getById($data['id']);

            $data['name'] = $role['name'];
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' role');
        } else {
            $this->addResponse('Error updating role.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeRole(array $data)
    {
        if (!$this->checkForSystemRole($data['id'])) {
            $this->addResponse('Cannot remove system role.', 1);

            return false;
        }

        if (isset($data['id'])) {
            $roleObj = $this->getFirst('id', $data['id']);

            if ($roleObj->getAccounts() && $roleObj->getAccounts()->count() > 0) {
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
            $componentsArr = $this->modules->components->getComponentsForApp($app['id']);

            if (count($componentsArr) > 0) {
                $components[strtolower($app['id'])] =
                    [
                        'title' => strtoupper($app['name']),
                        'id' => strtoupper($app['id'])
                    ];
                foreach ($componentsArr as $key => $component) {
                    $reflector = $this->annotations->get($component['class']);
                    $methods = $reflector->getMethodsAnnotations();

                    if ($methods) {
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
                if ($role['permissions'] && $role['permissions'] !== '') {
                    $permissionsArr = Json::decode($role['permissions'], true);
                } else {
                    $permissionsArr = [];
                }
                $permissions = [];

                foreach ($appsArr as $appKey => $app) {
                    $componentsArr = $this->modules->components->getComponentsForApp($app['id']);
                    foreach ($componentsArr as $key => $component) {
                        if ($component['class'] && $component['class'] !== '') {
                            $reflector = $this->annotations->get($component['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods) {
                                foreach ($methods as $annotation) {
                                    $action = $annotation->getAll('acl')[0]->getArguments();
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

                $this->packagesData->acls = Json::encode($acls);

                $role['permissions'] = Json::encode($permissions);

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
                $componentsArr = $this->modules->components->getComponentsForApp($app['id']);
                foreach ($componentsArr as $key => $component) {
                    //Build ACL Columns
                    if ($component['class'] && $component['class'] !== '') {
                        $reflector = $this->annotations->get($component['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods) {
                            foreach ($methods as $annotation) {
                                $action = $annotation->getAll('acl')[0]->getArguments();
                                $acls[$action['name']] = $action['name'];
                                $permissions[$app['id']][$component['id']][$action['name']] = 0;
                            }
                        }
                    }
                }
            }

            $this->packagesData->acls = Json::encode($acls);
            $role['permissions'] = Json::encode($permissions);
            $this->packagesData->role = $role;
        }

        $this->packagesData->apps = $appsArr;

        $this->packagesData->roles = $roles;

        return true;
    }

    public function searchRole(string $roleQueryString)
    {
        $searchRoles =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :aName:',
                    'bind'          => [
                        'aName'     => '%' . $roleQueryString . '%'
                    ]
                ]
            );

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