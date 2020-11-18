<?php

namespace Applications\Admin\Components\Role;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

/**
 * @RoleComponent
 */
class RoleComponent extends BaseComponent
{
    /**
     * @acl(name="view")
     */
    public function viewAction()
    {
        // $this->view->disable();
        $acls = [];
        $applicationsArr = $this->modules->applications->applications;
        $componentsArr = $this->modules->components->components;

        foreach ($applicationsArr as $applicationKey => $application) {
                // array_merge(
                //     $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                //     $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                // );

            $components[strtolower($application['name'])] = ['title' => strtoupper($application['name'])];
            foreach ($componentsArr as $key => $component) {
                $components[strtolower($application['name'])]['childs'][$component['type']] = ['title' => strtoupper($component['type'])];
            }
            foreach ($componentsArr as $key => $component) {
                // $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['application_id'] = $application['id'];
                $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['id'] = $component['id'];
                $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['title'] = $component['name'];
            }
        }
        // var_dump($components);
        $this->view->components = $components;

        $rolesArr = $this->roles->init()->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $roleKey => $roleValue) {
            $roles[$roleValue['id']] =
                [
                    'id'    => $roleValue['id'],
                    'name'  => $roleValue['name']
                ];
        }

        if (isset($this->getData()['id'])) {
            $role = $this->roles->getById($this->getData()['id']);

            if ($role) {

                if ($role['permissions'] && $role['permissions'] !== '') {
                    $permissionsArr = Json::decode($role['permissions'], true);
                } else {
                    $permissionsArr = [];
                }
                $permissions = [];

                foreach ($applicationsArr as $applicationKey => $application) {
                    // $componentsArr =
                    //     array_merge(
                    //         $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                    //         $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                    //     );
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
                        // if ($componentValue['type'] === 'listing') {
                        //     if (isset($permissionsArr[$componentValue['id']])) {
                        //         $permissions[$application['id']][$componentValue['id']] = $permissionsArr[$componentValue['id']];
                        //     } else {
                        //         $permissions[$application['id']][$componentValue['id']] = [0];
                        //     }
                        // } else if ($componentValue['type'] === 'crud') {
                        //     if (isset($permissionsArr[$componentValue['id']])) {
                        //         $permissions[$application['id']][$componentValue['id']] = $permissionsArr[$componentValue['id']];
                        //     } else {
                        //         $permissions[$application['id']][$componentValue['id']] = [0,0,0,0];
                        //     }
                        // }
                    }
                }
                $this->view->acls = Json::encode($acls);

                $role['permissions'] = Json::encode($permissions);

                $this->view->role = $role;

            } else {

                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Role ID Not Found!';

                return;
            }

        } else {

            $role = [];
            $permissions = [];

            foreach ($applicationsArr as $applicationKey => $application) {
                // $componentsArr =
                //     array_merge(
                //         $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                //         $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                //     );
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
                    // if ($component['type'] === 'listing') {
                    //     $permissions[$application['id']][$component['id']] = [0];
                    // } else if ($component['type'] === 'crud') {
                    //     $permissions[$application['id']][$component['id']] = [0,0,0,0];
                    // }
                }
            }

            $this->view->acls = Json::encode($acls);
            $role['permissions'] = Json::encode($permissions);
            $this->view->role = $role;
        }

        $this->view->roles = $roles;
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            $this->roles->addRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            $this->roles->updateRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->roles->removeRole($this->postData());

            $this->view->responseCode = $this->roles->packagesData->responseCode;

            $this->view->responseMessage = $this->roles->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}