<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class Accounts extends BasePackage
{
    protected $modelToUse = BasepackagesUsersAccounts::class;

    protected $packageName = 'accounts';

    public $accounts;

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addAccount(array $data)
    {
        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        if ($this->checkAccountByEmail($data['email'])) {
            $this->addResponse('Account for ID: ' . $data['email'] . ' already exists!', 1);

            return;
        }

        if ($this->validateData($data)) {

            $password = $this->generateNewPassword();

            $data['password'] = $this->secTools->hashPassword($password);

            if ($this->add($data)) {
                $id = $this->packagesData->last['id'];

                $this->addUpdateCanLogin($id, $data['can_login']);

                $data['id'] = $id;
                $this->basepackages->profile->addProfile($data);

                $this->updateRoleAccounts($data['role_id'], $id);

                if ($data['email_new_password'] === '1') {
                    $this->emailNewPassword($data['email'], $password);
                }

                $this->addActivityLog($data);

                $this->addResponse('Added new account for ID: ' . $data['email'], 0, null, true);

                $this->addToNotification('add', 'Added new account for ID: ' . $data['email']);
            }
        } else {
            $this->addResponse('Error adding account.', 1);
        }
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function updateAccount(array $data)
    {
        $accountObj = $this->modelToUse::findFirstById($data['id']);

        $account = $accountObj->toArray();

        if ($data['override_role'] == 0) {
            $data['permissions'] = Json::encode([]);
        }

        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {

            $password = $this->generateNewPassword();

            $data['password'] = $this->secTools->hashPassword($password);
        }

        if (isset($data['disable_two_fa']) && $data['disable_two_fa'] === '1') {
            $data['two_fa_status'] = null;
            $data['two_fa_secret'] = null;
        }

        if ($this->update($data)) {

            if (isset($data['can_login'])) {
                $this->addUpdateCanLogin($data['id'], $data['can_login']);
            }

            if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {
                $this->emailNewPassword($data['email'], $password);
            }

            $this->basepackages->profile->updateProfileViaAccount($data);

            $this->updateRoleAccounts($data['role_id'], $data['id'], $account['role_id']);

            $this->addActivityLog($data);

            $this->addResponse('Updated account for ID: ' . $data['email'], 0, null, true);

            $this->addToNotification('add', 'Updated account for ID: ' . $data['email']);

            if (isset($data['force_logout']) && $data['force_logout'] === '1') {
                $this->removeRelatedData($accountObj);
            }
        } else {
            $this->addResponse('Error updating account.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeAccount(array $data)
    {
        $accountObj = $this->modelToUse::findFirstById($data['id']);

        $account = $accountObj->toArray();

        $this->removeRoleAccount($account['role_id'], $account['id']);

        if (isset($data['id']) && $data['id'] != 1) {
            if ($this->remove($data['id'])) {

                if ($accountObj->getprofiles()) {
                    $accountObj->getprofiles()->delete();
                }

                $this->removeRelatedData($accountObj);

                $this->addToNotification('remove', 'Removed account for ID: ' . $account['email']);

                $this->addResponse('Removed account for ID: ' . $account['email']);
            } else {
                $this->addResponse('Error removing account.', 1);
            }
        } else {
            $this->addResponse('Cannot remove default account.', 1);
        }
    }

    protected function removeRelatedData($accountObj)
    {
        if ($accountObj->getcanlogin()) {
            $accountObj->getcanlogin()->delete();
        }
        if ($accountObj->gettunnels()) {
            $accountObj->gettunnels()->delete();
        }
        if ($accountObj->getidentifiers()) {
            $accountObj->getidentifiers()->delete();
        }
        if ($accountObj->getagents()) {
            $accountObj->getagents()->delete();
        }
        if ($accountObj->getsessions()) {
            $accountObj->getsessions()->delete();
        }
    }

    protected function addUpdateCanLogin($id, $canLogin)
    {
        if ($canLogin !== '') {
            $canLogin = Json::decode($canLogin, true);
        }

        if (count($canLogin) > 0) {
            foreach ($canLogin as $app => $allowed) {
                $canloginModel = new BasepackagesUsersAccountsCanlogin;
                $permission = $canloginModel::findFirst(['account_id = ' . $id . ' AND app = "' . $app . '"']);

                if ($permission) {
                    if ($allowed === true) {
                        $updatePermission['allowed'] = '1';
                    } else {
                        $updatePermission['allowed'] = '0';
                    }

                    $permission->assign($updatePermission);

                    $permission->update();
                } else {
                    $newPermission['account_id'] = $id;
                    $newPermission['app'] = $app;

                    if ($allowed === true) {
                        $newPermission['allowed'] = '1';
                    } else {
                        $newPermission['allowed'] = '0';
                    }

                    $canloginModel->assign($newPermission);

                    $canloginModel->create();
                }
            }
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

        if ($searchAccounts && count($searchAccounts) > 0) {
            $accounts = [];

            foreach ($searchAccounts as $accountKey => $accountValue) {
                if (in_array($accountValue['domain'], $domains)) {
                    $accounts[] =
                        ['id' => $accountValue['id'], 'email' => $accountValue['email']];
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->accounts = $accounts;

            return true;
        }

        return false;
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

    public function canLogin($id, $app)
    {
        $this->model = $this->modelToUse::findFirst($id);

        $canLogin =
            $this->model->canlogin->filter(
                function($allowed) use ($id, $app) {
                    $allowed = $allowed->toArray();

                    if ($allowed['account_id'] == $id &&
                        $allowed['app'] === $app &&
                        $allowed['allowed'] == true
                    ) {
                        return $allowed;
                    }
                }
            );

        if (count($canLogin) === 1) {
            return true;
        }

        return false;
    }

    public function hasSession($id, $session)
    {
        $this->model = $this->modelToUse::findFirst($id);

        $hasSession =
            $this->model->sessions->filter(
                function($sessionObj) use ($id, $session) {
                    $sessionObj = $sessionObj->toArray();

                    if ($sessionObj['account_id'] == $id &&
                        $sessionObj['session_id'] === $session
                    ) {
                        return $sessionObj;
                    }
                }
            );

        if (count($hasSession) === 1) {
            return true;
        }

        return false;
    }

    public function hasIdentifier($app, $identifier)
    {
        $identifierModel = new BasepackagesUsersAccountsIdentifiers;

        $identifier =
            $identifierModel->find(
                    [
                        'conditions'    => 'identifier = :identifier: AND app = :app:',
                        'bind'          =>
                            [
                                'identifier'  => $identifier,
                                'app'         => $app
                            ]
                    ],
                )->toArray();

        if (count($identifier) === 1) {
            return $identifier[0];
        } else {
            return false;
        }
    }

    public function checkAccountByNotificationsTunnelId($tunnelId)
    {
        $tunnelsModel = new BasepackagesUsersAccountsTunnels;

        $account = $tunnelsModel::find('[notifications_tunnel] = ' . $tunnelId)->toArray();

        if (count($account) === 1) {
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
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->basepackages->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = Json::encode([$email]);
        $emailData['subject'] = 'OTP for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $password;

        return $this->basepackages->emailqueue->addToQueue($emailData);
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
            $accountObj = $this->modelToUse::findFirstById($uid);

            if ($accountObj) {
                $account = $accountObj->toArray();

                $canLoginArr = $accountObj->canlogin->toArray();

                if ($canLoginArr > 0) {
                    foreach ($canLoginArr as $key => $value) {
                        $account['can_login'][$value['app']] = $value['allowed'];
                    }
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