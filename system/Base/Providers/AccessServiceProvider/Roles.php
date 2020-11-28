<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Roles as RolesModel;

class Roles extends BasePackage
{
    protected $modelToUse = RolesModel::class;

    protected $packageName = 'roles';

    public $roles;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function addRole(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new role.';
        }
    }

    public function updateRole(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating role.';
        }
    }

    public function removeRole(array $data)
    {
        if (isset($data['id']) && $data['id'] != 1) {

            $role = $this->getById($data['id']);

            $accounts = Json::decode($role['accounts'], true);

            if (count($accounts) > 0) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Role has accounts assigned to it. Cannot removes role.';

                return false;
            }

            if ($this->remove($data['id'])) {
                //Check accounts assigned to the role
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
        $applicationsArr = $this->modules->applications->applications;

        foreach ($applicationsArr as $applicationKey => $application) {
            $componentsArr = $this->modules->components->getComponentsForApplication($application['id']);
            $components[strtolower($application['name'])] = ['title' => strtoupper($application['name'])];
            // foreach ($componentsArr as $key => $component) {
            //     $components[strtolower($application['name'])]['childs'] = ['title' => strtoupper($component['type'])];
            // }
            foreach ($componentsArr as $key => $component) {
                $reflector = $this->annotations->get($component['class']);
                $methods = $reflector->getMethodsAnnotations();

                if ($methods) {
                    $components[strtolower($application['name'])]['childs'][$key]['id'] = $component['id'];
                    $components[strtolower($application['name'])]['childs'][$key]['title'] = $component['name'];
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

                foreach ($applicationsArr as $applicationKey => $application) {
                    $componentsArr = $this->modules->components->getComponentsForApplication($application['id']);
                    foreach ($componentsArr as $key => $component) {
                        if ($component['class'] && $component['class'] !== '') {
                            $reflector = $this->annotations->get($component['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods) {
                                foreach ($methods as $annotation) {
                                    $action = $annotation->getAll('acl')[0]->getArguments();
                                    $acls[$action['name']] = $action['name'];
                                    if (isset($permissionsArr[$component['id']])) {
                                        $permissions[$application['id']][$component['id']] = $permissionsArr[$component['id']];
                                    } else {
                                        $permissions[$application['id']][$component['id']][$action['name']] = 0;
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

            foreach ($applicationsArr as $applicationKey => $application) {
                $componentsArr = $this->modules->components->getComponentsForApplication($application['id']);
                foreach ($componentsArr as $key => $component) {
                    //Build ACL Columns
                    if ($component['class'] && $component['class'] !== '') {
                        $reflector = $this->annotations->get($component['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods) {
                            foreach ($methods as $annotation) {
                                $action = $annotation->getAll('acl')[0]->getArguments();
                                $acls[$action['name']] = $action['name'];
                                $permissions[$application['id']][$component['id']][$action['name']] = 0;
                            }
                        }
                    }
                }
            }

            $this->packagesData->acls = Json::encode($acls);
            $role['permissions'] = Json::encode($permissions);
            $this->packagesData->role = $role;
        }
        $this->packagesData->applications = $applicationsArr;

        $this->packagesData->roles = $roles;

        return true;
    }
}