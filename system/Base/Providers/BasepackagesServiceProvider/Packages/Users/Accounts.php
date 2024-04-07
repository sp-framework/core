<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
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

    public function getAccountById(int $id)
    {
        $this->setFFRelations(true);

        $this->getFirst('id', $id);

        if ($this->model) {
            $account = $this->model->toArray();

            $account['security'] = [];
            if ($this->model->getsecurity()) {
                $account['security'] = $this->model->getsecurity()->toArray();
            }

            $account['canlogin'] = [];
            if ($this->model->getcanlogin()) {
                $account['canlogin'] = $this->model->getcanlogin()->toArray();
            }

            $account['sessions'] = [];
            if ($this->model->getsessions()) {
                $account['sessions'] = $this->model->getsessions()->toArray();
            }

            $account['identifier'] = [];
            if ($this->model->getidentifiers()) {
                $account['identifier'] = $this->model->getidentifiers()->toArray();
            }

            $account['agents'] = [];
            if ($this->model->getagents()) {
                $account['agents'] = $this->model->getagents()->toArray();
            }

            $account['tunnels'] = [];
            if ($this->model->gettunnels()) {
                $account['tunnels'] = $this->model->gettunnels()->toArray();
            }

            $account['profile'] = [];
            if ($this->model->getProfile()) {
                $account['profile'] = $this->model->getProfile()->toArray();
            }

            $account['role'] = [];
            if ($this->model->getRole()) {
                $account['role'] = $this->model->getRole()->toArray();
            }

            $account['api'] = [];
            if ($this->model->getApi()) {
                $account['api'] = $this->model->getApi()->toArray();
                if ($account['api']['client_secret'] && $account['api']['client_secret'] !== '') {
                    $account['api']['client_secret'] = $this->secTools->decryptBase64($account['api']['client_secret']);
                }
            }

            return $account;
        } else {
            if ($this->ffData) {
                if ($this->ffData['api']['client_secret'] && $this->ffData['api']['client_secret'] !== '') {
                    $this->ffData['api']['client_secret'] = $this->secTools->decryptBase64($this->ffData['api']['client_secret']);
                }

                return $this->ffData;
            }
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
        $validation = $this->validateData($data);

        if ($validation !== true) {
            $this->addResponse($validation, 1);
            return;
        }

        if (!isset($data['status'])) {
            $data['status'] = '0';
        }

        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        $account = $this->checkAccountBy($data['email']);

        if ($account) {
            if (isset($data['package_name']) && $data['package_name'] !== 'profiles') {
                $account = array_merge($account, $data);

                $account['account_id'] = $account['id'];

                $this->updateAccount($account);

                $this->packagesData->last = $account;

                return;
            }

            $this->addResponse('Account for Email: ' . $data['email'] . ' already exists!', 1);

            return false;
        }

        if ($this->checkAccountBy($data['username'], false, 'username')) {
            $this->addResponse('Account for Username: ' . $data['username'] . ' already exists!', 1);

            return false;
        }

        $password = $this->basepackages->utils->generateNewPassword()['password'];

        $data['password'] = $this->secTools->hashPassword($password);

        $data['password_set_on'] = time();

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
        if (isset($data['forgotten_request']) && $data['forgotten_request'] == '1') {
            $validation = $this->validateData($data, true);
        } else {
            $validation = $this->validateData($data);
        }

        if ($validation !== true) {
            $this->addResponse($validation, 1);

            return false;
        }

        if (!isset($data['status'])) {
            $data['status'] = '0';
        }

        if ($this->auth->account() &&
            $data['id'] == $this->auth->account()['id']
        ) {
            if ($data['status'] == '0') {
                $data['status'] = 1;//Cannot disable own account
            }
        }

        $emailAccount = $this->checkAccountBy($data['email']);
        if ($emailAccount && $data['id'] != $emailAccount['id']) {
            $this->addResponse('Account for Email: ' . $data['email'] . ' already exists!', 1);

            return;
        }

        $usernameAccount = $this->checkAccountBy($data['username'], false, 'username');
        if ($usernameAccount && $data['id'] != $usernameAccount['id']) {
            $this->addResponse('Account for Username: ' . $data['username'] . ' already exists!', 1);

            return;
        }

        $accountObj = $this->getFirst('id', $data['id']);

        $account = $this->getAccountById($data['id']);

        if (!$account) {
            $this->addResponse('Account for does not exists!', 1);

            return;
        }

        if (!isset($data['override_role']) ||
            $data['override_role'] == 0
        ) {
            $data['permissions'] = $this->helper->encode([]);
        } else if (isset($data['override_role']) && $data['override_role'] == 1) {
            //Prevent lockout of logged in user
            if ($this->auth->account() &&
                $data['id'] == $this->auth->account()['id']
            ) {
                if (is_string($data['permissions'])) {
                    $data['permissions'] = $this->helper->decode($data['permissions'], true);
                }

                $component = $this->modules->components->getComponentByRoute('system/users/accounts');

                $data['permissions'][$this->apps->getAppInfo()['id']][$component['id']]['view'] = 1;
                $data['permissions'][$this->apps->getAppInfo()['id']][$component['id']]['update'] = 1;

                $data['permissions'] = $this->helper->encode($data['permissions']);
            }
        }

        $data['email'] = strtolower($data['email']);

        $data['domain'] = explode('@', $data['email'])[1];

        //We keep the old password intact. If user remembers their password and logs in with that, we destroy this code.
        if (isset($data['forgotten_request']) && $data['forgotten_request'] == '1') {
            $password = $this->basepackages->utils->generateNewPassword()['password'];
            $data['forgotten_request_code'] = $this->secTools->hashPassword($password);
        } else if (isset($data['email_new_password']) && $data['email_new_password'] === '1') {
            $password = $this->basepackages->utils->generateNewPassword()['password'];
            $data['password'] = $this->secTools->hashPassword($password);
        }

        if (isset($data['disable_twofa_otp']) && $data['disable_twofa_otp'] === '1') {
            $data['twofa_otp_status'] = null;
            $data['twofa_otp_secret'] = null;
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

            if ($this->auth->account()) {
                $this->basepackages->profile->updateProfileViaAccount($data);
            }

            $this->addUpdateSecurity($account['id'], $data);

            $this->addActivityLog($data);

            $this->addToNotification('add', 'Updated account for ID: ' . $data['email']);

            $this->addResponse('Updated account for ID: ' . $data['email'], 0, null, true);

            if ($this->auth->account() &&
                $data['id'] == $this->auth->account()['id']
            ) {//Cannot logout yourself!
                $data['force_logout'] = '0';
            }

            if ((isset($data['force_logout']) && $data['force_logout'] === '1') ||
                 (isset($data['status']) && $data['status'] === '0')
            ) {
                $this->removeRelatedData($accountObj, $account, false, false);
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

            $accountObj = $this->getFirst('id', (int) $data['id']);

            if ($accountObj && $this->config->databasetype === 'db') {
                $account = $accountObj->toArray();

                if ($accountObj->id != $data['id']) {
                    $this->addResponse('Account with id not found', 1);

                    return false;
                }

                if ($this->remove($data['id'])) {
                    $this->addToNotification('remove', 'Removed account for ID: ' . $account['email']);

                    $this->addResponse('Removed account for ID: ' . $account['email']);

                    return true;
                } else {
                    $this->addResponse('Error removing account.', 1);
                }
            } else if ($this->ffStore && $this->ffData) {
                $account = $this->ffData;

                if ($this->ffData['id'] != $data['id']) {
                    $this->addResponse('Account with id not found', 1);

                    return false;
                }

                if ($this->remove($data['id'])) {
                    $this->addToNotification('remove', 'Removed account for ID: ' . $account['email']);

                    $this->addResponse('Removed account for ID: ' . $account['email']);

                    return true;
                } else {
                    $this->addResponse('Error removing account.', 1);
                }
            } else {
                $this->addResponse('Account with id not found', 1);
            }
        } else {
            $this->addResponse('Cannot remove default account.', 1);
        }
    }

    public function registerAccount(array $data)
    {
        if (!$this->app['registration_allowed'] || $this->app['registration_allowed'] == '0') {
            $this->addResponse('Registration for this application is disabled. Please contact administrator.', 1);

            return;
        }

        $data['role_id'] = $this->app['registration_role_id'];
        $data['email_new_password'] = '1';
        $data['override_role'] = '0';
        $data['permissions'] = $this->helper->encode([]);
        $data['force_pwreset'] = '1';
        $data['status'] = '1';

        $data['email'] = strtolower($data['email']);
        $data['username'] = str_replace('@', '.', $data['email']);

        $canLogin = '1';

        if ($this->app['approve_accounts_manually'] == '1') {
            $canLogin = '0';
            $data['status'] = '0';
        }

        $data['can_login'] = $this->helper->encode(
            [
                strtolower($this->app['id']) => $canLogin
            ]
        );

        $validation = $this->validateData($data);

        if ($validation === true) {
            if ($this->addAccount($data)) {
                $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

                $this->packagesData->redirectUrl = $this->links->url('auth');
            }

            $this->logger->log->alert($this->packagesData->responseMessage);
        } else {
            $this->addResponse($validation, 1);

            return;
        }
    }

    public function removeRelatedData(
        $accountObj,
        $account = null,
        $security = true,
        $canlogin = true,
        $sessions = true,
        $identifiers = true,
        $agents = true,
        $tunnels = true,
        $api = true
    ) {
        if ($security) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getsecurity()
            ) {
                $accountObj->getsecurity()->delete();
            } else {
                if ($account['security'] &&
                    is_array($account['security']) &&
                    count($account['security']) > 0
                ) {
                    $securityStore = $this->ff->store((new BasepackagesUsersAccountsSecurity)->getSource());
                    $securityCheck = $securityStore->findById($account['security']['id']);

                    if ($securityCheck) {
                        $securityStore->deleteById($account['security']['id'], false);
                    }
                }
            }
        }

        if ($canlogin) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getcanlogin()
            ) {
                $accountObj->getcanlogin()->delete();
            } else {
                if ($account['canlogin'] &&
                    is_array($account['canlogin']) &&
                    count($account['canlogin']) > 0
                ) {
                    $canloginStore = $this->ff->store((new BasepackagesUsersAccountsCanlogin)->getSource());
                    foreach ($account['canlogin'] as $canloginValue) {
                        $canloginValueCheck = $canloginStore->findById($canloginValue['id']);

                        if ($canloginValueCheck) {
                            $canloginStore->deleteById($canloginValue['id'], false);
                        }
                    }
                }
            }
        }

        if ($identifiers) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getidentifiers()
            ) {
                $accountObj->getidentifiers()->delete();
            } else {
                if ($account['identifier'] &&
                    is_array($account['identifier']) &&
                    count($account['identifier']) > 0
                ) {
                    $identifierStore = $this->ff->store((new BasepackagesUsersAccountsIdentifiers)->getSource());
                    $identifierCheck = $identifierStore->findById($account['identifier']['id']);

                    if ($identifierCheck) {
                        $identifierStore->deleteById($account['identifier']['id'], false);
                    }
                }
            }
        }

        if ($agents) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getagents()
            ) {
                $accountObj->getagents()->delete();
            } else {
                if ($account['agents'] &&
                    is_array($account['agents']) &&
                    count($account['agents']) > 0
                ) {
                    $agentsStore = $this->ff->store((new BasepackagesUsersAccountsAgents)->getSource());
                    foreach ($account['agents'] as $agentsValue) {
                        $agentsValueCheck = $agentsStore->findById($agentsValue['id']);

                        if ($agentsValueCheck) {
                            $agentsStore->deleteById($agentsValue['id'], false);
                        }
                    }
                }
            }
        }

        if ($sessions) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getsessions()
            ) {
                $accountObj->getsessions()->delete();
            } else {
                if ($account['sessions'] &&
                    is_array($account['sessions']) &&
                    count($account['sessions']) > 0
                ) {
                    $sessionsStore = $this->ff->store((new BasepackagesUsersAccountsSessions)->getSource());
                    foreach ($account['sessions'] as $sessionsValue) {
                        $sessionsValueCheck = $sessionsStore->findById($sessionsValue['id']);

                        if ($sessionsValueCheck) {
                            $sessionsStore->deleteById($sessionsValue['id'], false);
                        }
                    }
                }
            }
        }

        if ($tunnels) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->gettunnels()
            ) {
                $accountObj->gettunnels()->delete();
            } else {
                if ($account['tunnels'] &&
                    is_array($account['tunnels']) &&
                    count($account['tunnels']) > 0
                ) {
                    $tunnelsStore = $this->ff->store((new BasepackagesUsersAccountsTunnels)->getSource());
                    $tunnelsCheck = $tunnelsStore->findById($account['tunnels']['id']);

                    if ($tunnelsCheck) {
                        $tunnelsStore->deleteById($account['tunnels']['id'], false);
                    }
                }
            }
        }

        if ($api) {
            if ($this->config->databasetype === 'db' &&
                $accountObj->getApi()
            ) {
                $accountObj->getApi()->delete();
            } else {
                if ($account['api'] &&
                    is_array($account['api']) &&
                    count($account['api']) > 0
                ) {
                    $apiStore = $this->ff->store((new ServiceProviderApiClients)->getSource());
                    $apiCheck = $apiStore->findById($account['api']['id']);

                    if ($apiCheck) {
                        $apiStore->deleteById($account['api']['id'], false);
                    }
                }
            }
        }

        return true;
    }

    public function addUpdateSecurity($id, $data)
    {
        $securityModel = new BasepackagesUsersAccountsSecurity;

        if ($this->config->databasetype === 'db') {
            $account = $securityModel::findFirst(['account_id = ' . $id]);
        } else {
            $securityStore = $this->ff->store($securityModel->getSource());

            $account = $securityStore->findOneBy(['account_id', '=', $id]);
        }

        $data['account_id'] = $id;

        unset($data['id']);

        if ($account) {
            if ($this->config->databasetype === 'db') {
                $account->assign($data);

                $account->update();

                return true;
            } else {
                $data['id'] = $account['id'];

                $securityStore->update($data);

                return true;
            }
        } else {
            if ($this->config->databasetype === 'db') {
                $securityModel->assign($data);

                $securityModel->create();

                return true;
            } else {
                $securityStore->insert($data);

                return true;
            }
        }

        return false;
    }

    public function addUpdateCanLogin($id, $canLogin)
    {
        if ($canLogin !== '') {
            $canLogin = $this->helper->decode($canLogin, true);

            if (count($canLogin) > 0) {
                $canloginModel = new BasepackagesUsersAccountsCanlogin;

                if ($this->config->databasetype === 'db') {
                } else {
                    $canloginStore = $this->ff->store($canloginModel->getSource());
                }

                foreach ($canLogin as $appId => $allowed) {
                    if ($this->config->databasetype === 'db') {
                        $permission = $canloginModel::findFirst(['account_id = ' . $id . ' AND app_id = "' . $appId . '"']);
                    } else {
                        $permission = $canloginStore->findOneBy([['account_id', '=', (int) $id], ['app_id', '=', $appId]]);
                    }

                    if ($permission) {
                        if ($this->config->databasetype === 'db') {
                            $updatePermission['allowed'] = $allowed;

                            $permission->assign($updatePermission);

                            $permission->update();
                        } else {
                            $permission['allowed'] = $allowed;

                            $canloginStore->update($permission);
                        }
                    } else {
                        $newPermission['account_id'] = $id;
                        $newPermission['app_id'] = $appId;
                        $newPermission['allowed'] = $allowed;

                        if ($this->config->databasetype === 'db') {
                            $canloginModel->assign($newPermission);

                            $canloginModel->create();
                        } else {
                            $canloginStore->insert($newPermission);
                        }
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

        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'email LIKE :aEmail:',
                    'bind'          => [
                        'aEmail'     => '%' . $emailQueryString . '%'
                    ]
                ];
        } else {
            $conditions =
                [
                    'conditions' => ['email', 'LIKE', '%' . $emailQueryString . '%']
                ];
        }

        $searchAccounts = $this->getByParams($conditions);

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

    public function checkAccount(string $username, $getSecurity = false)
    {
        if ($this->app) {
            $account = $this->checkAccountBy($username, $getSecurity);

            if ($this->app['acceptable_usernames'] && $this->app['acceptable_usernames'] !== '') {
                if (is_string($this->app['acceptable_usernames'])) {
                    $this->app['acceptable_usernames'] = $this->helper->decode($this->app['acceptable_usernames'], true);
                }

                foreach ($this->app['acceptable_usernames'] as $acceptableUsername) {
                    if ($acceptableUsername === 'email') {
                        $acccountEmail = $this->checkAccountBy($username, $getSecurity);

                        if ($acccountEmail) {
                            return $acccountEmail;
                        }
                    } else if ($acceptableUsername === 'username') {
                        $accountUsername = $this->checkAccountBy($username, $getSecurity, 'username');

                        if ($accountUsername) {
                            return $accountUsername;
                        }
                    } else {
                        $userId = str_replace($acceptableUsername, '', $username);

                        if ($userId == '' || $userId == 0) {
                            continue;
                        } else {
                            $accountUserId = $this->checkAccountBy($userId, $getSecurity, 'id');

                            if ($accountUserId) {
                                return $accountUserId;
                            }
                        }
                    }
                }

                return false;
            } else {
                return $account;
            }
        } else {
            return $account;
        }
    }

    public function checkAccountBy(string $username, $getSecurity = false, $by = 'email')
    {
        if ($getSecurity) {
            $this->setFFRelations(true);
        }

        $this->getFirst($by, $username, true);

        if ($this->model) {
            $account = $this->model->toArray();

            if ($getSecurity && $this->model->getsecurity()) {
                $account['security'] = $this->model->getsecurity()->toArray();
            }
        }

        if ($this->ffData) {
            $account = $this->ffData;
        }

        if (isset($account)) {
            return $account;
        } else {
            return false;
        }
    }

    public function canLogin($id, $appId)
    {
        $this->getById($id);

        if ($this->model) {
            $canLogin =
                $this->model->canlogin->filter(
                    function($allowed) use ($id, $appId) {
                        $allowed = $allowed->toArray();

                        if ($allowed['account_id'] == $id &&
                            $allowed['app_id'] === $appId
                        ) {
                            return $allowed;
                        }
                    }
                );
        }

        if ($this->ffData) {
            $this->ffStoreToUse = 'basepackages_users_accounts_canlogin';

            $this->getByParams(['conditions' => [['account_id', '=', $id],['app_id', '=', $appId]]]);

            $canLogin = $this->ffData;
        }

        if (!$canLogin) {
            return false;
        }

        if (count($canLogin) === 1 &&
            ($canLogin[0]['allowed'] == '1' || $canLogin[0]['allowed'] == '2')
        ) {
            return true;
        } else if (count($canLogin) === 1 && $canLogin[0]['allowed'] == '0') {
            return $canLogin[0];
        } else if (count($canLogin) > 1) {
            $this->logger->log->debug('Multiple login entries for user :' . $id . ' are wrong');

            return true;//Sending true as we dont want to make more entries.
        }

        return false;
    }

    public function hasSession($id, $session)
    {
        $this->getById($id);

        if ($this->model) {
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
        }

        if ($this->ffData) {
            $this->ffStoreToUse = 'basepackages_users_accounts_sessions';

            $this->getByParams(['conditions' => [['account_id', '=', $id],['session_id', '=', $session]]]);

            $hasSession = $this->ffData;
        }

        if (count($hasSession) === 1) {
            return true;
        }

        return false;
    }

    public function hasIdentifier($app, $identifier)
    {
        $identifierModel = new BasepackagesUsersAccountsIdentifiers;

        if ($this->config->databasetype === 'db') {
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
        } else {
            $identifiersStore = $this->ff->store($identifierModel->getSource());

            $identifier = $identifiersStore->findBy([['identifier', '=', $identifier], ['app', '=', $app]]);
        }

        if (count($identifier) === 1) {
            return $identifier[0];
        } else {
            return false;
        }
    }

    public function checkAccountByNotificationsTunnelId($tunnelId)
    {
        $tunnelsModel = new BasepackagesUsersAccountsTunnels;

        if ($this->config->databasetype === 'db') {
            $account = $tunnelsModel::find('[notifications_tunnel] = ' . $tunnelId)->toArray();
        } else {
            $tunnelsStore = $this->ff->store($tunnelsModel->getSource());

            $account = $tunnelsStore->findBy(['notifications_tunnel', '=', $tunnelId]);
        }

        if (count($account) === 1) {
            return $account[0];
        } else {
            return false;
        }
    }

    public function validateData(array $data, $forgotRequest = false)
    {
        $this->validation->init();
        $this->validation->add('email', PresenceOf::class, ["message" => "Enter valid username."]);
        $this->validation->add('email', Email::class, ["message" => "Enter valid username."]);

        if (!$forgotRequest) {
            $this->validation->add('first_name', PresenceOf::class, ["message" => "Enter valid first name."]);
            $this->validation->add('last_name', PresenceOf::class, ["message" => "Enter valid last name."]);
        }

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
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$email]);
        $emailData['subject'] = 'OTP for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $password;

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }

    public function removeAccountAgents(array $data)
    {
        $agentModel = new BasepackagesUsersAccountsAgents;
        $agentStore = $this->ff->store($agentModel->getSource());
        $sessionModel = new BasepackagesUsersAccountsSessions;
        $sessionStore = $this->ff->store($sessionModel->getSource());

        if (isset($data['id'])) {
            if ($this->config->databasetype === 'db') {
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
            } else {
                $agent = $agentStore->findById($data['id']);

                if ($agent) {
                    if ($agent['session_id'] === $this->session->getId()) {
                        $this->addResponse('Cannot remove this session.', 1);

                        return;
                    }

                    $session = $sessionStore->findOneBy(['session_id', '=', $agent['session_id']]);

                    if ($session) {
                        $sessionStore->deleteById($session['id']);
                    }

                    if ($agentStore->deleteById($agent['id'])) {
                        $this->addResponse('Removed!');
                    } else {
                        $this->addResponse('Error removing', 1);
                    }

                    return;
                }
            }
        } else if (isset($data['verified'])) {
            if (!$this->auth->account()) {
                $this->addResponse('Account Not Found!');

                return;
            }

            if ($this->config->databasetype === 'db') {
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
            } else {
                $agents = $agentStore->findBy([['account_id', '=', $this->auth->account()['id']], ['verified', '=', $data['verified']]]);

                if ($agents) {
                    $removed = true;

                    foreach ($agents as $key => $agent) {
                        if ($agent['session_id'] !== $this->session->getId() ||
                            ($agent['session_id'] === $this->session->getId() &&
                             $agent['verified'] == '0')
                        ) {
                            $session = $sessionStore->findOneBy(['session_id', '=', $agent['session_id']]);

                            if ($session) {
                                $sessionStore->deleteById($session['id']);
                            }

                            if (!$agentStore->deleteById($agent['id'])) {
                                $removed = false;
                            }
                        }
                    }

                    if ($data['verified'] == '1') {
                        $sessions = $sessionStore->findBy(['account_id', '=', $this->auth->account()['id']]);

                        if ($sessions) {
                            foreach ($sessions as $session) {
                                if ($session['session_id'] !== $this->session->getId()) {

                                    if (!$sessionStore->deleteById($session['id'])) {
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
        }

        $this->addResponse('Id/all & verified not set.', 1);
    }

    public function generateViewData(int $uid = null)
    {
        $acls = [];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

            if (count($componentsArr) > 0) {
                $components[strtolower($app['id'])] =
                    [
                        'title' => strtoupper($app['name']),
                        'id' => strtoupper($app['id'])
                    ];

                foreach ($componentsArr as $key => $component) {
                    $reflector = $this->annotations->get($component['class']);
                    $methods = $reflector->getMethodsAnnotations();

                    if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
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
            $account = $this->getAccountById($uid);

            if ($account) {
                if ($account['canlogin'] > 0) {
                    foreach ($account['canlogin'] as $key => $value) {
                        $account['can_login'][$value['app_id']] = $value['allowed'];
                    }
                } else {
                    $account['can_login'] = [];
                }

                if ($account['security']['permissions'] && $account['security']['permissions'] !== '') {
                    if (is_string($account['security']['permissions'])) {
                        $permissionsArr = $this->helper->decode($account['security']['permissions'], true);
                    }
                } else {
                    $permissionsArr = [];
                }

                $permissions = [];

                foreach ($appsArr as $appKey => $app) {
                    $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                    foreach ($componentsArr as $key => $component) {
                        if ($component['class'] && $component['class'] !== '') {
                            $reflector = $this->annotations->get($component['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
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
                $this->packagesData->acls = $this->helper->encode($acls);

                $account['permissions'] = $this->helper->encode($permissions);
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
                $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                foreach ($componentsArr as $key => $component) {
                    //Build ACL Columns
                    if ($component['class'] && $component['class'] !== '') {
                        $reflector = $this->annotations->get($component['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
                            foreach ($methods as $annotation) {
                                $action = $annotation->getAll('acl')[0]->getArguments();
                                $acls[$action['name']] = $action['name'];
                                $permissions[$app['id']][$component['id']][$action['name']] = 0;
                            }
                        }
                    }
                }
            }

            $this->packagesData->acls = $this->helper->encode($acls);
            $account['security']['permissions'] = $this->helper->encode($permissions);
            $this->packagesData->account = $account;
        }

        $this->packagesData->apps = $appsArr;

        $this->packagesData->roles = $roles;

        return true;
    }
}