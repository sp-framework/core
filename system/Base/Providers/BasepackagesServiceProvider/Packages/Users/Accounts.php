<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSecurity;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class Accounts extends BasePackage
{
    protected $modelToUse = BasepackagesUsersAccounts::class;

    protected $packageName = 'accounts';

    public $accounts;

    public function getAccountById(
        int $id,
        $getsecurity = false,
        $getcanlogin = false,
        $getsessions = false,
        $getidentifiers = false,
        $getagents = false,
        $gettunnels = false,
        $getprofiles = false
    ) {
        $this->getFirst('id', $id);

        if ($this->model) {
            $account = $this->model->toArray();

            if ($getsecurity) {
                if ($this->model->getsecurity()) {
                    $relationData = $this->model->getsecurity()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($getcanlogin) {
                if ($this->model->getcanlogin()) {
                    $relationData = $this->model->getcanlogin()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($getsessions) {
                if ($this->model->getsessions()) {
                    $relationData = $this->model->getsessions()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($getidentifiers) {
                if ($this->model->getidentifiers()) {
                    $relationData = $this->model->getidentifiers()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($getagents) {
                if ($this->model->getagents()) {
                    $relationData = $this->model->getagents()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($gettunnels) {
                if ($this->model->gettunnels()) {
                    $relationData = $this->model->gettunnels()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            if ($getprofiles) {
                if ($this->model->getProfiles()) {
                    $relationData = $this->model->getProfiles()->toArray();

                    unset($relationData['id']);

                    $account = array_merge($account, $relationData);
                }
            }

            return $account;
        }

        return null;
    }

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addAccount(array $data)
    {
        if (!isset($data['status'])) {
            $data['status'] = '0';
        }

        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        if ($this->checkAccountByEmail($data['email'])) {
            if (isset($data['package_name']) && $data['package_name'] !== 'profiles') {

                $account = $this->checkAccountByEmail($data['email']);

                $account = array_merge($account, $data);

                $account['account_id'] = $account['id'];

                $this->updateAccount($account);

                $this->packagesData->last = $account;

                return;
            }

            $this->addResponse('Account for ID: ' . $data['email'] . ' already exists!', 1);

            return;
        }

        if ($this->validateData($data) === true) {
            $password = $this->generateNewPassword();

            $data['password'] = $this->secTools->hashPassword($password);

            if (!isset($data['package_name'])) {
                $data['package_name'] = 'profiles';
            }
            if (!isset($data['package_row_id'])) {
                $data['package_row_id'] = '0';
            }

            if ($this->add($data)) {
                $id = $this->packagesData->last['id'];

                $this->addUpdateCanLogin($id, $data['can_login']);

                $data['id'] = $id;

                $this->addUpdateSecurity($id, $data);

                $this->basepackages->profile->addProfile($data);

                if ($data['package_name'] === 'profiles') {
                    $data['package_row_id'] = $this->basepackages->profile->packagesData->responseData['id'];

                    $this->update($data);
                }

                if (isset($data['email_new_password']) &&
                    $data['email_new_password'] == '1'
                ) {
                    $this->emailNewPassword($data['email'], $password);
                }

                $this->addActivityLog($data);

                $this->addToNotification('add', 'Added new account for ID: ' . $data['email']);

                $this->addResponse('Added new account for ID: ' . $data['email'], 0, null, true);
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
        if (!isset($data['status'])) {
            $data['status'] = '0';
        }

        $accountObj = $this->getFirst('id', $data['id']);

        $account = $this->getAccountById($data['id'], true);

        if (!isset($data['override_role']) ||
            $data['override_role'] == 0
        ) {
            $data['permissions'] = Json::encode([]);
        } else if (isset($data['override_role']) && $data['override_role'] == 1) {
            //Prevent lockout of logged in user
            if ($data['id'] == $this->auth->account()['id']) {
                if (is_string($data['permissions'])) {
                    $data['permissions'] = Json::decode($data['permissions'], true);
                }

                $component = $this->modules->components->getComponentByRoute('system/users/accounts');

                $data['permissions'][$this->apps->getAppInfo()['id']][$component['id']]['view'] = 1;
                $data['permissions'][$this->apps->getAppInfo()['id']][$component['id']]['update'] = 1;

                $data['permissions'] = Json::encode($data['permissions']);
            }
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

        if (!isset($data['package_name']) || 
            $data['package_name'] === ''
        ) {
            $data['package_name'] = 'profiles';
        }

        if ($this->update($data)) {
            if (isset($data['can_login'])) {
                $this->addUpdateCanLogin($data['id'], $data['can_login']);
            }

            if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {
                $this->emailNewPassword($data['email'], $password);
            }

            $this->basepackages->profile->updateProfileViaAccount($data);

            $this->addUpdateSecurity($account['id'], $data);

            $this->addActivityLog($data);

            $this->addToNotification('add', 'Updated account for ID: ' . $data['email']);

            $this->addResponse('Updated account for ID: ' . $data['email'], 0, null, true);

            if ((isset($data['force_logout']) && $data['force_logout'] === '1') ||
                 (isset($data['status']) && $data['status'] === '0')
            ) {
                $this->removeRelatedData($accountObj, false, false);
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
        if (isset($data['id']) && $data['id'] != 1) {
            if ($this->auth->account()['id'] === $data['id']) {
                $this->addResponse('Cannot remove own account!', 1);

                return;
            }

            $accountObj = $this->getFirst('id', $data['id']);

            if ($accountObj) {
                $account = $accountObj->toArray();

                $relationData = $accountObj->getSecurity()->toArray();

                unset($relationData['id']);

                $account = array_merge($account, $relationData);

                if ($this->remove($data['id'])) {
                    $this->removeRelatedData($accountObj);

                    $this->addToNotification('remove', 'Removed account for ID: ' . $account['email']);

                    $this->addResponse('Removed account for ID: ' . $account['email']);
                } else {
                    $this->addResponse('Error removing account.', 1);
                }
            } else {
                $this->addResponse('Error removing account.', 1);
            }
        } else {
            $this->addResponse('Cannot remove default account.', 1);
        }
    }

    public function removeRelatedData(
        $accountObj,
        $security = true,
        $canlogin = true,
        $sessions = true,
        $identifiers = true,
        $agents = true,
        $tunnels = true
    ) {
        if ($security) {
            if ($accountObj->getsecurity()) {
                var_dump($accountObj->getsecurity()->toArray());die();
                $accountObj->getsecurity()->delete();
            }
        }

        if ($canlogin) {
            if ($accountObj->getcanlogin()) {
                $accountObj->getcanlogin()->delete();
            }
        }

        if ($identifiers) {
            if ($accountObj->getidentifiers()) {
                $accountObj->getidentifiers()->delete();
            }
        }

        if ($agents) {
            if ($accountObj->getagents()) {
                $accountObj->getagents()->delete();
            }
        }

        if ($sessions) {
            if ($accountObj->getsessions()) {
                $accountObj->getsessions()->delete();
            }
        }

        if ($tunnels) {
            if ($accountObj->gettunnels()) {
                $accountObj->gettunnels()->delete();
            }
        }
    }

    protected function addUpdateSecurity($id, $data)
    {
        $securityModel = new BasepackagesUsersAccountsSecurity;

        $account = $securityModel::findFirst(['account_id = ' . $id]);

        $data['account_id'] = $id;

        unset($data['id']);

        if ($account) {
            $account->assign($data);

            $account->update();
        } else {
            $securityModel->assign($data);

            $securityModel->create();
        }
    }

    protected function addUpdateCanLogin($id, $canLogin)
    {
        if ($canLogin !== '') {
            $canLogin = Json::decode($canLogin, true);

            if (count($canLogin) > 0) {
                foreach ($canLogin as $appId => $allowed) {
                    $canloginModel = new BasepackagesUsersAccountsCanlogin;
                    $permission = $canloginModel::findFirst(['account_id = ' . $id . ' AND app_id = "' . $appId . '"']);

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
                        $newPermission['app_id'] = $appId;

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

    public function checkAccountByEmail(string $email, $getSecurity = false)
    {
        $this->getFirst('email', $email, true);

        if ($this->model) {
            $account = $this->model->toArray();

            if ($this->model->getsecurity()) {
                $security = $this->model->getsecurity()->toArray();

                unset($security['id']);
                unset($security['account_id']);

                $account = array_merge($account, $security);
            }
        }

        if (isset($account)) {
            return $account;
        } else {
            return false;
        }
    }

    public function canLogin($id, $appID)
    {
        $this->getById($id);

        $canLogin =
            $this->model->canlogin->filter(
                function($allowed) use ($id, $appID) {
                    $allowed = $allowed->toArray();

                    if ($allowed['account_id'] == $id &&
                        $allowed['app_id'] === $appID &&
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
        $this->getById($id);

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

    public function validateData(array $data)
    {
        $this->validation->add('first_name', PresenceOf::class, ["message" => "Enter valid first name."]);
        $this->validation->add('last_name', PresenceOf::class, ["message" => "Enter valid last name."]);
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
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = Json::encode([$email]);
        $emailData['subject'] = 'OTP for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $password;

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }

    public function removeAccountAgents(array $data)
    {
        $agentModel = new BasepackagesUsersAccountsAgents;
        $sessionModel = new BasepackagesUsersAccountsSessions;

        if (isset($data['id'])) {
            $agentObj = $agentModel::findFirstById($data['id']);

            if ($agentObj) {
                if ($agentObj->session_id === $this->session->getId()) {
                    $this->addResponse('Cannot remove this session.', 1);

                    return;
                }

                $sessionObj = $sessionModel::findFirstBysession_id($agentObj->session_id);

                if ($sessionObj) {
                    if ($sessionObj->identifiers) {
                        $sessionObj->identifiers->delete();
                    }

                    $sessionObj->delete();
                }

                if ($agentObj->delete()) {
                    $this->addResponse('Removed!');
                } else {
                    $this->addResponse('Error removing', 1);
                }

                return;
            }
        } else if (isset($data['verified'])) {
            if (!$this->auth->account()) {
                $this->addResponse('Account Not Found!');

                return;
            }

            $agentObj = $agentModel::find(
                [
                    'conditions'    => 'account_id = :aid: AND verified = :v:',
                    'bind'          => [
                        'aid'       => $this->auth->account()['id'],
                        'v'         => $data['verified']
                    ]
                ]
            );

            if ($agentObj) {
                $removed = true;
                foreach ($agentObj as $agent) {
                    if ($agent->session_id !== $this->session->getId() ||
                        ($agent->session_id === $this->session->getId() &&
                         $agent->verified == '0')
                    ) {
                        $sessionObj = $sessionModel::findFirstBysession_id($agent->session_id);

                        if ($sessionObj) {

                            if ($sessionObj->identifiers) {
                                $sessionObj->identifiers->delete();
                            }

                            $sessionObj->delete();
                        }

                        if (!$agent->delete()) {
                            $removed = false;
                        }
                    }
                }

                if ($data['verified'] == '1') {
                    $sessionObj = $sessionModel::find(
                        [
                            'conditions'    => 'account_id = :aid:',
                            'bind'          => [
                                'aid'       => $this->auth->account()['id'],
                            ]
                        ]
                    );

                    if ($sessionObj) {
                        foreach ($sessionObj as $session) {
                            if ($session->session_id !== $this->session->getId()) {
                                if ($session->identifiers) {
                                    $session->identifiers->delete();
                                }
                                if (!$session->delete()) {
                                    $removed = false;
                                }
                            }
                        }
                    }
                }

                if ($removed) {
                    $this->addResponse('Removed!');
                } else {
                    $this->addResponse('Error removing session', 1);
                }

                return;
            }
        }

        $this->addResponse('Id/all & verified not set.', 1);
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
                $account = $this->getAccountById($uid, true);

                $canLoginArr = $accountObj->canlogin->toArray();

                if ($canLoginArr > 0) {
                    foreach ($canLoginArr as $key => $value) {
                        $account['can_login'][$value['app_id']] = $value['allowed'];
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