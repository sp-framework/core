<?php

namespace Applications\Admin\Components\Role;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class RoleComponent extends BaseComponent
{
    public function viewAction()
    {
        // $this->view->disable();
        $applicationsArr = $this->modules->applications->applications;

        foreach ($applicationsArr as $applicationKey => $application) {
            $componentsArr =
                array_merge(
                    $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                    $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                );
            $components[strtolower($application['name'])] = ['title' => strtoupper($application['name'])];
            $components[strtolower($application['name'])]['childs']['listing'] = ['title' => 'LISTINGS'];
            $components[strtolower($application['name'])]['childs']['crud'] = ['title' => 'FORMS'];
            foreach ($componentsArr as $key => $component) {
                $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['id'] = $component['id'];
                $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['title'] = $component['name'];
            }
        }

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
                unset($roles[$this->getData()['id']]); //Remove Self from parents list
                if ($role['permissions'] && $role['permissions'] !== '') {
                    $permissionsArr = Json::decode($role['permissions'], true);
                } else {
                    $permissionsArr = [];
                }
                $permissions = [];

                foreach ($applicationsArr as $applicationKey => $application) {
                    $componentsArr =
                        array_merge(
                            $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                            $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                        );
                    foreach ($componentsArr as $componentKey => $componentValue) {
                        if ($componentValue['type'] === 'listing') {
                            if (isset($permissionsArr[$componentValue['id']])) {
                                $permissions[$application['id']][$componentValue['id']] = $permissionsArr[$componentValue['id']];
                            } else {
                                $permissions[$application['id']][$componentValue['id']] = [0];
                            }
                        } else if ($componentValue['type'] === 'crud') {
                            if (isset($permissionsArr[$componentValue['id']])) {
                                $permissions[$application['id']][$componentValue['id']] = $permissionsArr[$componentValue['id']];
                            } else {
                                $permissions[$application['id']][$componentValue['id']] = [0,0,0,0];
                            }
                        }
                    }
                }

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
                $componentsArr =
                    array_merge(
                        $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                        $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                    );
                foreach ($componentsArr as $componentKey => $componentValue) {
                    if ($componentValue['type'] === 'listing') {
                        $permissions[$application['id']][$componentValue['id']] = [0];
                    } else if ($componentValue['type'] === 'crud') {
                        $permissions[$application['id']][$componentValue['id']] = [0,0,0,0];
                    }
                }
            }

            $role['permissions'] = Json::encode($permissions);

            $this->view->role = $role;
        }

        $this->view->roles = $roles;
    }

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