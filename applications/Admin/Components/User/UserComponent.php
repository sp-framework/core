<?php

namespace Applications\Admin\Components\User;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class UserComponent extends BaseComponent
{
    public function viewAction()
    {
        $applicationsArr = $this->modules->applications->applications;

        $applications = [];

        foreach ($applicationsArr as $applicationKey => $application) {
            $applications[$applicationKey] = strtolower($application['name']);

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

        $this->view->roles = $roles;

        if (isset($this->getData()['id'])) {
            $user = $this->users->getById($this->getData()['id']);

            if ($user) {
                $user['permissions'] = Json::decode($user['permissions'], true);

                $permissions = [];

                foreach ($applicationsArr as $applicationKey => $application) {
                    if (isset($user['permissions']['permissions'])) {
                        $permissionArr = $user['permissions']['permissions'];

                        $componentsArr =
                            array_merge(
                                $this->modules->components->getComponentsForApplicationAndType($application['id'], 'listing'),
                                $this->modules->components->getComponentsForApplicationAndType($application['id'], 'crud')
                            );

                        foreach ($componentsArr as $componentKey => $componentValue) {
                            if ($componentValue['type'] === 'listing') {
                                if (isset($permissionArr[$componentValue['id']])) {
                                    if ($user['override_role'] === '1') {
                                        $permissions[$application['id']][$componentValue['id']] = $permissionArr[$componentValue['id']];
                                    } else {
                                        $permissions[$application['id']][$componentValue['id']] = [0];
                                    }
                                } else {
                                    $permissions[$application['id']][$componentValue['id']] = [0];
                                }
                            } else if ($componentValue['type'] === 'crud') {
                                if (isset($permissionArr[$componentValue['id']])) {
                                    if ($user['override_role'] === '1') {
                                        $permissions[$application['id']][$componentValue['id']] = $permissionArr[$componentValue['id']];
                                    } else {
                                        $permissions[$application['id']][$componentValue['id']] = [0,0,0,0];
                                    }
                                } else {
                                    $permissions[$application['id']][$componentValue['id']] = [0,0,0,0];
                                }
                            }
                        }
                    }
                }
                $user['permissions']['permissions'] = Json::encode($permissions);

                $this->view->user = $user;

            } else {

                $this->view->responseCode = 1;

                $this->view->responseMessage = 'User ID Not Found!';

                return;
            }

        } else {
            $user = [];
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

            $user['permissions']['permissions'] = Json::encode($permissions);

            $this->view->user = $user;
        }
        // $this->view->disable();
        $this->view->applications = $applications;
    }

    public function addAction()
    {
        if ($this->request->isPost()) {

            $this->users->addUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {

            $this->users->updateUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->users->removeUser($this->postData());

            $this->view->responseCode = $this->users->packagesData->responseCode;

            $this->view->responseMessage = $this->users->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}