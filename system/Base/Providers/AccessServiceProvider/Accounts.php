<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Accounts as AccountsModel;

class Accounts extends BasePackage
{
    protected $modelToUse = AccountsModel::class;

    protected $packageName = 'accounts';

    public $accounts;

    public function addAccount(array $data)
    {
        $data['email'] = strtolower($data['email']);

        if ($this->checkAccountByEmail($data['email'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Account already exists!';

            return false;
        }

        if ($this->validateData($data)) {

            $password = $this->generateNewPassword();


            $data['password'] = $this->secTools->hashPassword($password);

            $newAccount = $this->add($data);

            if ($newAccount) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Account added';

                if ($data['email_new_password'] === '1') {
                    if ($this->emailNewPassword($data['email'], $password) !== true) {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error sending email for new password.';
                    }
                }

                $id = $this->packagesData->last['id'];

                $this->updateRoleAccounts($data['role_id'], $id);

                return true;
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding account.';

            return false;
        }

        return true;
    }

    public function updateAccount(array $data)
    {
        $account = $this->getById($data['id']);

        if (!$account) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Account Not Found.';

            return false;
        }

        $data['email'] = strtolower($data['email']);

        if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {

            $password = $this->generateNewPassword();

            $data['password'] = $this->secTools->hashPassword($password);
        }

        if (isset($data['force_logout']) && $data['force_logout'] === '1') {
            $data['session_id'] = null;
        }

        if ($this->update($data)) {

            if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {
                if (!$this->emailNewPassword($data['email'], $password)) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error sending email for new password.';
                }
            }

            $this->updateRoleAccounts($data['role_id'], $data['id'], $account['role_id']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['email'] . ' account';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating account.';
        }
    }

    public function removeAccount(array $data)
    {
        $account = $this->getById($data['id']);

                $this->removeRoleAccount($account['role_id'], $account['id']);
        if (isset($data['id']) && $data['id'] != 1) {

            if ($this->remove($data['id'])) {

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed account';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing account.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove default account.';
        }
    }

    public function checkAccountByEmail(string $email)
    {
        $account =
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

        if ($account) {
            return $account[0];
        } else {
            return false;
        }
    }

    // public function checkAccountByIdentifier(string $rememberIdentifier)
    // {
    //     $account =
    //         $this->getByParams(
    //                 [
    //                     'conditions'    => 'remember_identifier = :ri:',
    //                     'bind'          =>
    //                         [
    //                             'ri'  => $rememberIdentifier
    //                         ]
    //                 ],
    //                 false,
    //                 false
    //             );

    //     if ($account) {
    //         return $account[0];
    //     } else {
    //         return false;
    //     }
    // }

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

    protected function generateNewPassword()
    {
        return $this->random->base62(12);
    }

    protected function emailNewPassword($email, $password)
    {
        if ($this->email->setup()) {
            $emailSettings = $this->email->getEmailSettings();

            $this->email->setSender($emailSettings['from_address'], $emailSettings['from_address']);
            $this->email->setRecipientTo($email, $email);
            $this->email->setSubject('OTP for ' . $this->basepackages->domains->getDomain()['name']);
            $this->email->setBody($password);

            return $this->email->sendNewEmail();
        } else {
            return false;
        }
    }

    protected function updateRoleAccounts(int $rid, int $id, int $oldRid = null)
    {
        if ($oldRid) {
            $oldRole = $this->roles->getById($oldRid);

            if ($oldRole['accounts']) {
                $oldRole['accounts'] = Json::decode($oldRole['accounts'], true);

                $key = array_keys($oldRole['accounts'], $id);
                if ($key) {
                    unset($oldRole['accounts'][$key[0]]);
                }

                $oldRole['accounts'] = Json::encode($oldRole['accounts']);

                $this->roles->update($oldRole);
            }
        }
        $role = $this->roles->getById($rid);

        if ($role['accounts']) {
            $role['accounts'] = Json::decode($role['accounts'], true);
        } else {
            $role['accounts'] = [];
        }

        array_push($role['accounts'], $id);

        $role['accounts'] = Json::encode($role['accounts']);

        $this->roles->update($role);
    }

    protected function removeRoleAccount(int $rid, int $id)
    {
        $role = $this->roles->getById($rid);

        $role['accounts'] = Json::decode($role['accounts'], true);

        $accountKey = array_keys($role['accounts'], $id);

        if (count($accountKey) === 0) {
            return;
        }

        unset($role['accounts'][$accountKey[0]]);

        $role['accounts'] = Json::encode($role['accounts']);

        $this->roles->update($role);
    }

    public function generateViewData(int $uid = null)
    {
        if (isset($this->basepackages->domains->getDomain()['applications'][$this->application['id']]['email_service']) &&
            $this->basepackages->domains->getDomain()['applications'][$this->application['id']]['email_service'] !== ''
        ) {
            $this->packagesData->canEmail = true;
        } else {
            $this->packagesData->canEmail = false;
        }

        $acls = [];
        $applicationsArr = $this->modules->applications->applications;

        foreach ($applicationsArr as $applicationKey => $application) {
            $componentsArr = $this->modules->components->getComponentsForApplication($application['id']);
            $components[strtolower($application['name'])] = ['title' => strtoupper($application['name'])];
            // foreach ($componentsArr as $key => $component) {
            //     $components[strtolower($application['name'])]['childs'][$component['type']] = ['title' => strtoupper($component['type'])];
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
            $account = $this->getById($uid);

            if ($account) {

                if ($account['can_login'] && $account['can_login'] !== '') {
                    $account['can_login'] = Json::decode($account['can_login'], true);
                } else {
                    $account['can_login'] = [];
                }

                if ($account['permissions'] && $account['permissions'] !== '') {
                    $permissionsArr = Json::decode($account['permissions'], true);
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

                $account['permissions'] = Json::encode($permissions);

                $this->packagesData->account = $account;

            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Account Not Found!';

                return false;
            }

        } else {
            $account = [];
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
            $account['permissions'] = Json::encode($permissions);
            $this->packagesData->account = $account;
        }

        $this->packagesData->applications = $applicationsArr;

        $this->packagesData->roles = $roles;

        return true;
    }
}