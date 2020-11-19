<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Users as UsersModel;

class Users extends BasePackage
{
    protected $modelToUse = UsersModel::class;

    protected $packageName = 'users';

    public $users;

    public function addUser(array $data)
    {
        $data['email'] = strtolower($data['email']);

        if ($this->checkUserByEmail($data['email'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Account already exists!';

            return false;
        }

        if ($this->validateData($data)) {

            $password = $this->random->base62(12);

            $data['password'] = $this->secTools->hashPassword($password);

            $newUser = $this->add($data);

            if ($newUser) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Account added';

                if ($data['email_new_password'] === '1') {
                    $this->emailNewPassword($data['email'], $password);
                }

                $id = $this->packagesData->last['id'];

                $this->updateRoleUsers($data['role_id'], $id);

                return true;
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding user account.';

            return false;
        }

        return true;
    }

    public function updateUser(array $data)
    {
        $user = $this->getById($data['id']);

        if (!$user) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'User Not Found.';

            return false;
        }

        $data['email'] = strtolower($data['email']);

        if ($data['email_new_password'] === '1') {

            $password = $this->random->base62(12);

            $data['password'] = $this->secTools->hashPassword($password);
        }

        if ($data['force_logout'] === '1') {
            $data['session_id'] = null;
        }

        if ($this->update($data)) {

            if ($data['email_new_password'] === '1') {
                $this->emailNewPassword($data['email'], $password);
            }

            $this->updateRoleUsers($data['role_id'], $data['id'], $user['role_id']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['email'] . ' user';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating user.';
        }
    }

    public function removeUser(array $data)
    {
        if (isset($data['id']) && $data['id'] != 1) {

            if ($this->remove($data['id'])) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed user';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing user.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove default user.';
        }
    }

    public function checkUserByEmail(string $email)
    {
        $user =
            $this->getByParams(
                    [
                        'conditions'    => 'email = :email:',
                        'bind'          =>
                            [
                                'email'  => $email
                            ]
                    ],
                    false,
                    false
                );

        if ($user) {
            return $user[0];
        } else {
            return false;
        }
    }

    public function checkUserByIdentifier(string $rememberIdentifier)
    {
        $user =
            $this->getByParams(
                    [
                        'conditions'    => 'remember_identifier = :ri:',
                        'bind'          =>
                            [
                                'ri'  => $rememberIdentifier
                            ]
                    ],
                    false,
                    false
                );

        if ($user) {
            return $user[0];
        } else {
            return false;
        }
    }

    protected function validateData(array $data)
    {
        $this->validation->add('email', PresenceOf::class, ["message" => "Enter valid username."]);
        $this->validation->add('email', Email::class, ["message" => "Enter valid username."]);

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    protected function emailNewPassword($email, $password)
    {
        $this->email->setup(
            [
                'enabled'   =>  'true',
                'host'      =>  'mail.bazaari.com.au',
                'port'      => 465,
                'auth'      => true,
                'allow_html_body'=> true,
                'username'  => 'no-reply@bazaari.com.au',
                'test_email_address'  => 'guru@bazaari.com.au',
                'password'  => 'ddU{&]ga3MQ&',
                'encryption'=> 'true',
            ]
        );

        $this->email->setSender('guru@bazaari.com.au', 'Guru');
        $this->email->setRecipientTo($email, $email);
        $this->email->setSubject('Testing Email');
        $this->email->setBody($password);
        $this->email->sendNewEmail();
    }

    protected function updateRoleUsers(int $rid, int $id, int $oldRid = null)
    {
        if ($oldRid) {
            $oldRole = $this->roles->getById($oldRid);

            if ($oldRole['users']) {
                $oldRole['users'] = Json::decode($oldRole['users'], true);

                $key = array_keys($oldRole['users'], $id);
                if ($key) {
                    unset($oldRole['users'][$key[0]]);
                }

                $oldRole['users'] = Json::encode($oldRole['users']);

                $this->roles->update($oldRole);
            }
        }
        $role = $this->roles->getById($rid);

        if ($role['users']) {
            $role['users'] = Json::decode($role['users'], true);
        } else {
            $role['users'] = [];
        }

        array_push($role['users'], $id);

        $role['users'] = Json::encode($role['users']);

        $this->roles->update($role);
    }

    public function generateViewData(int $uid = null)
    {
        $acls = [];
        $applicationsArr = $this->modules->applications->applications;

        foreach ($applicationsArr as $applicationKey => $application) {
            $componentsArr = $this->modules->components->getComponentsForApplication($application['id']);
            $components[strtolower($application['name'])] = ['title' => strtoupper($application['name'])];
            foreach ($componentsArr as $key => $component) {
                $components[strtolower($application['name'])]['childs'][$component['type']] = ['title' => strtoupper($component['type'])];
            }
            foreach ($componentsArr as $key => $component) {
                $reflector = $this->annotations->get($component['class']);
                $methods = $reflector->getMethodsAnnotations();

                if ($methods) {
                    $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['id'] = $component['id'];
                    $components[strtolower($application['name'])]['childs'][$component['type']]['childs'][$key]['title'] = $component['name'];
                }
            }
        }

        $this->packagesData->components = $components;

        $rolesArr = $this->roles->init()->getAll()->roles;
        $roles = [];
        foreach ($rolesArr as $roleKey => $roleValue) {
            $roles[$roleValue['id']] =
                [
                    'id'    => $roleValue['id'],
                    'name'  => $roleValue['name']
                ];
        }

        if ($uid) {
            $user = $this->getById($uid);

            if ($user) {

                if ($user['can_login'] && $user['can_login'] !== '') {
                    $user['can_login'] = Json::decode($user['can_login'], true);
                } else {
                    $user['can_login'] = [];
                }

                if ($user['permissions'] && $user['permissions'] !== '') {
                    $permissionsArr = Json::decode($user['permissions'], true);
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

                $user['permissions'] = Json::encode($permissions);

                $this->packagesData->user = $user;

            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'User Not Found!';

                return false;
            }

        } else {
            $user = [];
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
            $user['permissions'] = Json::encode($permissions);
            $this->packagesData->user = $user;
        }

        $this->packagesData->applications = $applicationsArr;

        $this->packagesData->roles = $roles;

        return true;
    }
}