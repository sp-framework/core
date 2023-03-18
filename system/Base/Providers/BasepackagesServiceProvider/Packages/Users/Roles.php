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

    public function get(array $data = [], bool $resetCache = false)
    {
        if (count($data) === 0) {
            return $this->packages;
        }

        if (isset($data['id'])) {
            $role = $this->getById($data['id']);

            if ($role) {
                return $role;
            }
        }

        return false;
    }

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function add(array $data)
    {
        if ($this->addToDb($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new role.';
        }
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function update(array $data)
    {
        if ($this->updateToDb($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating role.';
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function remove(array $data)
    {
        if (isset($data['id']) &&
            ($data['id'] != 1 && $data['id'] != 2 && $data['id'] != 3)
        ) {
            $roleObj = $this->getFirst('id', $data['id']);

            if ($roleObj->getAccounts() && $roleObj->getAccounts()->count() > 0) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Role has accounts assigned to it. Cannot removes role.';

                return false;
            }

            if ($this->removeFromDb($data['id'], true, false)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed role';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing role.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove default role.';
        }
    }

    public function generateViewData(int $rid = null)
    {
        $acls = [];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            $componentsArr = $this->modules->components->get(['app_id' => $app['id']]);

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

        $rolesArr = $this->get();
        $roles = [];
        foreach ($rolesArr as $roleKey => $roleValue) {
            $roles[$roleValue['id']] =
                [
                    'id'    => $roleValue['id'],
                    'name'  => $roleValue['name']
                ];
        }

        if ($rid) {
            $role = $this->get(['id' => $rid]);

            if ($role) {
                if ($role['permissions'] && $role['permissions'] !== '') {
                    $permissionsArr = Json::decode($role['permissions'], true);
                } else {
                    $permissionsArr = [];
                }
                $permissions = [];

                foreach ($appsArr as $appKey => $app) {
                    $componentsArr = $this->modules->components->get(['app_id' => $app['id']]);
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
                $componentsArr = $this->modules->components->get(['app_id' => $app['id']]);
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