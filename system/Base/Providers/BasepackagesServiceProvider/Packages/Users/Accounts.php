<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class Accounts extends BasePackage
{
    protected $modelToUse = BasepackagesUsersAccounts::class;

    protected $packageName = 'accounts';

    public $accounts;

    public function addAccount(array $data)
    {
        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

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

                $id = $this->packagesData->last['id'];

                $data['id'] = $id;
                $this->basepackages->profile->addProfile($data);

                $this->updateRoleAccounts($data['role_id'], $id);

                if ($data['email_new_password'] === '1') {
                    if ($this->emailNewPassword($data['email'], $password) !== true) {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error sending email for new password.';
                    }
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Account added';

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

        if ($data['override_role'] == 0) {
            $data['permissions'] = Json::encode([]);
        }

        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {

            $password = $this->generateNewPassword();

            $data['password'] = $this->secTools->hashPassword($password);
        }

        if (isset($data['force_logout']) && $data['force_logout'] === '1') {
            $data['session_ids'] = null;
        }

        if (isset($data['disable_two_fa']) && $data['disable_two_fa'] === '1') {
            $data['two_fa_status'] = null;
            $data['two_fa_secret'] = null;
        }

        if ($this->update($data)) {

            if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {
                if (!$this->emailNewPassword($data['email'], $password)) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error sending email for new password.';
                }
            }

            $this->basepackages->profile->updateProfileViaAccount($data);

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

    public function searchAccountInternal(string $emailQueryString)
    {
        // Only search accounts with email domain that are registered with the system
        $domains = [];

        foreach ($this->domains->domains as $key => $domain) {
            $domains[$key] = $domain['name'];
        }

        $searchAccounts =
            $this->getByParams(
                [
                    'conditions'    => 'email LIKE :aEmail:',
                    'bind'          => [
                        'aEmail'     => '%' . $emailQueryString . '%'
                    ]
                ]
            );

        if (count($searchAccounts) > 0) {
            $accounts = [];

            foreach ($searchAccounts as $accountKey => $accountValue) {
                if (in_array($accountValue['domain'], $domains)) {
                    $accounts[$accountKey]['id'] = $accountValue['id'];
                    $accounts[$accountKey]['email'] = $accountValue['email'];
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->accounts = $accounts;

            return true;
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
            $this->email->setSubject('OTP for ' . $this->domains->getDomain()['name']);
            $this->email->setBody($password);

            return $this->email->sendNewEmail();
        } else {
            return false;
        }
    }

    protected function updateRoleAccounts(int $rid, int $id, int $oldRid = null)
    {
        if ($oldRid) {
            $oldRole = $this->basepackages->roles->getById($oldRid);

            if ($oldRole['accounts']) {
                $oldRole['accounts'] = Json::decode($oldRole['accounts'], true);

                $key = array_keys($oldRole['accounts'], $id);
                if ($key) {
                    unset($oldRole['accounts'][$key[0]]);
                }

                $oldRole['accounts'] = Json::encode($oldRole['accounts']);

                $this->basepackages->roles->update($oldRole);
            }
        }
        $role = $this->basepackages->roles->getById($rid);

        if ($role['accounts']) {
            $role['accounts'] = Json::decode($role['accounts'], true);
        } else {
            $role['accounts'] = [];
        }

        array_push($role['accounts'], $id);

        $role['accounts'] = Json::encode($role['accounts']);

        $this->basepackages->roles->update($role);
    }

    protected function removeRoleAccount(int $rid, int $id)
    {
        $role = $this->basepackages->roles->getById($rid);

        $role['accounts'] = Json::decode($role['accounts'], true);

        $accountKey = array_keys($role['accounts'], $id);

        if (count($accountKey) === 0) {
            return;
        }

        unset($role['accounts'][$accountKey[0]]);

        $role['accounts'] = Json::encode($role['accounts']);

        $this->basepackages->roles->update($role);
    }

    public function generateViewData(int $uid = null)
    {
        if (isset($this->domains->getDomain()['apps'][$this->app['id']]['email_service']) &&
            $this->domains->getDomain()['apps'][$this->app['id']]['email_service'] !== '' &&
            $this->domains->getDomain()['apps'][$this->app['id']]['email_service'] !== 0
        ) {
            $this->packagesData->canEmail = true;
        } else {
            $this->packagesData->canEmail = false;
        }

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
                // foreach ($componentsArr as $key => $component) {
                //     $components[strtolower($app['name'])]['childs'] = ['title' => strtoupper($component['type'])];
                // }
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

        $rolesArr = $this->basepackages->roles->init()->getAll()->roles;
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

                $account['permissions'] = Json::encode($permissions);

                $account['profile'] = $this->basepackages->profile->getProfile($account['id']);

                $this->packagesData->account = $account;

            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Account Not Found!';

                return false;
            }

        } else {
            $account = [];
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
            $account['permissions'] = Json::encode($permissions);
            $this->packagesData->account = $account;
        }

        $this->packagesData->apps = $appsArr;

        $this->packagesData->roles = $roles;

        return true;
    }
}