<?php

namespace System\Base\Providers\AccessServiceProvider\Access;

use Carbon\Carbon;
use Phalcon\Filter\Validation\Validator\Confirmation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Filter\Validation\Validator\StringLength\Min;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Access\Auth\Password;
use System\Base\Providers\AccessServiceProvider\Access\Auth\TwoFa;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;

class Auth extends BasePackage
{
    public $twoFa;

    public $password;

    protected $key = null;

    protected $separator = '|';

    protected $oldSessionId;

    protected $cookieKey;

    protected $account = null;

    protected $app;

    protected $otp;

    public function init()
    {
        $this->app = $this->apps->getAppInfo();

        $this->cookieKey = 'remember_' . $this->getKey();

        $this->twoFa = new TwoFa();

        $this->password = new Password();

        return $this;
    }

    public function login(array $data)
    {
        $validate = $this->validateData($data, 'auth');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (PHP_SAPI === 'cli') {
            if (!$this->checkAccount($data)) {
                return false;
            }

            return true;
        }

        if (!$this->checkAccount($data)) {//Set $this->account here
            $this->access->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $validate = $this->validateData($data, 'auth2fa');

        if ($validate !== true) {
            if (str_contains(strtolower($validate), '2fa code')) {
                if (str_contains(strtolower($validate), 'please contact administrator')) {
                    $validate = str_replace('Error! Please contact administrator.', '', $validate);
                }

                if ($this->account['security']['twofa_otp_status'] == true) {
                    $this->addResponse($validate, 3, ['allowed_methods' => $this->core->core['settings']['security']['twofaSettings']['twofaUsing']]);
                } else {//redirect to setup twofa
                    $this->addResponse('2FA needed, but not set. Redirecting...');

                    $this->packagesData->redirectUrl = $this->links->url('auth/q/setup2fa/true');

                    return true;
                }
            } else {
                $this->addResponse($validate, 1);
            }

            return false;
        }

        $this->access->ipFilter->removeFromMonitoring();

        $security = $this->getAccountSecurityObject();

        if (isset($this->core->core['settings']['security']['twofa']) &&
            $this->core->core['settings']['security']['twofa'] == 'true' &&
            isset($this->app['enforce_2fa']) &&
            $this->app['enforce_2fa'] == '1'
        ) {
            if (!$this->twoFa->validateTwoFaCode($security, $data)) {
                $this->addResponse(
                    $this->twoFa->packagesData->responseMessage,
                    $this->twoFa->packagesData->responseCode,
                    $this->twoFa->packagesData->responseData ?? []
                );

                return false;
            }
        }

        $this->addResponse('Authenticated. Redirecting...');

        if (($this->account['security']['force_pwreset'] &&
             $this->account['security']['force_pwreset'] == '1') ||
            (isset($this->account['security']['force_pwreset_after']) &&
             $this->account['security']['force_pwreset_after'] !== null &&
             $this->account['security']['force_pwreset_after'] != '0' &&
             time() > $this->account['security']['force_pwreset_after'] &&
             $this->core->core['settings']['security']['passwordPolicy'] == 'true' &&
             isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter'] > 0)
        ) {
            $this->packagesData->redirectUrl = $this->links->url('auth/q/pwreset/true');

            return true;
        }

        if ($this->secTools->passwordNeedsRehash($this->account['security']['password'])) {
            $this->account['security']['password'] = $this->secTools->hashPassword($data['pass'], $this->config->security->passwordWorkFactor);
        }

        if ($this->core->core['settings']['security']['passwordPolicy'] == 'true') {
            if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter']) &&
                (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter'] > 0
            ) {
                $passwordSetOn = Carbon::now();

                if (isset($this->account['security']['password_set_on']) &&
                    (int) $this->account['security']['password_set_on'] > 0
                ) {
                    $passwordSetOn = Carbon::createFromTimestamp((int) $this->account['security']['password_set_on']);
                }

                $this->account['security']['force_pwreset_after'] =
                    $passwordSetOn->addDays((int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter'])->getTimestamp();
            } else {
                $this->account['security']['force_pwreset_after'] = null;
            }
        } else {
            $this->account['security']['force_pwreset_after'] = null;
        }

        $this->basepackages->accounts->addUpdateSecurity($this->account['id'], $this->account['security']);

        $this->basepackages->accounts->checkEnv($this->account['id']);

        $this->setSessionAndRecaller($data);

        $this->setUserIdCooikie();

        if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
            $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
        } else {
            $this->packagesData->redirectUrl = $this->links->url('home');
        }

        if ($this->opCache && $this->opCache->checkCache('account_' . $this->account['id'], 'core')) {
            $this->opCache->removeCache('account_' . $this->account['id'], 'core');
        }

        $this->logger->log->debug($this->account['email'] . ' authenticated successfully on app ' . $this->app['name']);

        return true;
    }

    public function logout($forced = false)
    {
        if (!$this->account) {
            try {
                $this->setUserFromSession();
            } catch (\Exception $e) {
                $this->sessionTools->clearSession($this->session->getId());

                return;
            }
        }

        $this->clearAccountRecaller();

        $this->clearAccountSessionId();

        $this->cookies->reset();

        if ($this->opCache && $this->opCache->checkCache('account_' . $this->account['id'], 'core')) {
            $this->opCache->removeCache('account_' . $this->account['id'], 'core');
        }

        if ($this->session->has('_PHCOOKIE_' . $this->cookieKey)) {
            $this->session->remove('_PHCOOKIE_' . $this->cookieKey);
        }

        if ($this->session->has($this->key)) {
            $this->session->remove($this->key);
        }

        if (!$forced) {
            $this->session->redirectUrl = '/';

            $this->packagesData->redirectUrl = $this->links->url('/');

            $this->logger->log->debug($this->account['email'] . ' logged out successfully from app: ' . $this->apps->getAppInfo()['name']);
        }

        return true;
    }

    protected function clearAccountRecaller()
    {
        if ($this->config->databasetype === 'db') {
            $identifierModel = new BasepackagesUsersAccountsIdentifiers;

            $identifier = $identifierModel::findFirst(
                [
                'session_id = :sessionId:',
                'bind'      => ['sessionId' => $this->session->getId()]
                ]
            );

            if ($identifier) {
                if (!$identifier->delete()) {
                    $this->logger->log->debug($identifier->getMessages());
                }
            }
        } else {
            $identifierStore = $this->ff->store('basepackages_users_accounts_identifiers');

            $identifierStore->findOneBy(['session_id', '=', $this->session->getId()]);

            if ($identifierStore->toArray()) {
                $identifierStore->deleteById($identifierStore->toArray()['id']);
            }
        }

        $this->clearRecallerCookies();
    }

    protected function clearAccountSessionId()
    {
        $sessionModel = new BasepackagesUsersAccountsSessions;
        $sessionStore = $this->ff->store($sessionModel->getSource());

        if ($this->config->databasetype === 'db') {
            $session = $sessionModel::find(
                [
                'account_id = :accountId: AND app = :app:',
                'bind'      =>
                    [
                        'accountId'     => $this->account['id'],
                        'app'           => $this->getKey()
                    ]
                ]
            );

            if ($session) {
                if (!$session->delete()) {
                    $this->logger->log->debug($session->getMessages());
                }
            }
        } else {
            $sessions = $sessionStore->findBy([['account_id', '=', $this->account['id']], ['app', '=', $this->getKey()]]);

            if ($sessions && count($sessions) > 0) {
                foreach ($sessions as $session) {
                    $sessionStore->deleteById($session['id'], true, false, ['agents']);
                }
            }
        }

        $this->sessionTools->removeSessionKey($this->getKey());
    }

    public function checkAccount(array $data, $viaProfile = null)
    {
        $account = $this->basepackages->accounts->checkAccount($data['user'], true);

        if ($account) {
            if ($account['status'] != '1') {
                $this->addResponse('Error: Username/Password incorrect!', 1);

                $this->logger->log->debug($data['user'] . ' is disabled!');

                return false;
            }

            //New App OR New account via rego
            $canLogin = $this->basepackages->accounts->canLogin($account['id'], $this->app['id']);

            if ($canLogin === false ||
                ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '2')
            ) {
                if ($this->app['can_login_role_ids']) {
                    if (is_string($this->app['can_login_role_ids'])) {
                        $this->app['can_login_role_ids'] = $this->helper->decode($this->app['can_login_role_ids'], true);
                    }

                    if (in_array($account['security']['role_id'], $this->app['can_login_role_ids'])) {
                        if ($canLogin === false) {
                            if ($this->config->databasetype === 'db') {
                                $canloginModel = new BasepackagesUsersAccountsCanlogin;

                                $newLogin['account_id'] = $account['id'];
                                $newLogin['app_id'] = $this->app['id'];
                                $newLogin['allowed'] = '2';

                                $canloginModel->assign($newLogin);

                                $canloginModel->create();
                            } else {
                                $canloginStore = $this->ff->store('basepackages_users_accounts_canlogin');

                                $canloginStore->insert(
                                    [
                                        'account_id'    => $account['id'],
                                        'app_id'        => $this->app['id'],
                                        'allowed'       => 2
                                    ]
                                );
                            }
                        }
                    } else {
                        $this->addResponse('Error: Contact System Administrator', 1);

                        $this->logger->log->debug($account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                        return false;
                    }
                } else {
                    $this->addResponse('Error: Contact System Administrator', 1);

                    $this->logger->log->debug('App\'s can_login_role_ids not set for app ' . $this->app['name']);

                    return false;
                }
            } else if ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '0') {
                $this->addResponse('Error: Contact System Administrator', 1);

                $this->logger->log->debug($account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                return false;
            }

            if (!$this->secTools->checkPassword($data['pass'], $account['security']['password'])) {//Password Fail
                if ($account['security']['forgotten_request'] == true) {
                    if (time() > $account['security']['forgotten_request_sent_on'] + ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForgottenPasswordTimeout'] ?? 60)
                    ) {
                        $account['security']['forgotten_request'] = null;
                        $account['security']['forgotten_request_session_id'] = null;
                        $account['security']['forgotten_request_ip'] = null;
                        $account['security']['forgotten_request_agent'] = null;
                        $account['security']['forgotten_request_code'] = null;
                        $account['security']['forgotten_request_sent_on'] = null;
                        $this->basepackages->accounts->addUpdateSecurity($account['id'], $account['security']);
                        $this->addResponse('Code Expired! Request new code...', 1);

                        return false;
                    }

                    if ($account['security']['forgotten_request_session_id'] !== $this->session->getId() ||
                        $account['security']['forgotten_request_ip'] !== $this->request->getClientAddress() ||
                        $account['security']['forgotten_request_agent'] !== $this->request->getUserAgent()
                    ) {
                        $this->addResponse('Error: OTP entered on a different browser than requested!', 1);

                        return false;
                    }

                    if ($this->secTools->checkPassword($data['pass'], $account['security']['forgotten_request_code'])) {//forgotten success and we remove forgotten fields
                        $account['security']['forgotten_request'] = null;
                        $account['security']['forgotten_request_session_id'] = null;
                        $account['security']['forgotten_request_ip'] = null;
                        $account['security']['forgotten_request_agent'] = null;
                        $account['security']['forgotten_request_code'] = null;
                        $account['security']['forgotten_request_sent_on'] = null;
                        $this->basepackages->accounts->addUpdateSecurity($account['id'], $account['security']);

                        return true;
                    }
                }

                if ($viaProfile) {
                    $this->addResponse('Error: Current Password incorrect!', 1);
                } else {
                    $this->addResponse('Error: Username/Password incorrect!', 1);
                }

                $this->logger->log->debug('Incorrect username/password entered by account ' . $account['email'] . ' on app ' . $this->app['name']);

                return false;
            }

            if ($account['security']['forgotten_request'] == true) {//We remove this as the user now remembers their password and logs in with it.
                $account['security']['forgotten_request'] = null;
                $account['security']['forgotten_request_session_id'] = null;
                $account['security']['forgotten_request_ip'] = null;
                $account['security']['forgotten_request_agent'] = null;
                $account['security']['forgotten_request_code'] = null;
                $this->basepackages->accounts->addUpdateSecurity($account['id'], $account['security']);
            }

            $this->account = $account;
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the account exists.

            $this->addResponse('Error: Username/Password incorrect!', 1);

            $this->logger->log->debug($data['user'] . ' is not in DB. App: ' . $this->app['name']);

            //This is where we do something with too many login attempts, we can move it to iptables and block the IP or put them in a honeypot.
            return false;
        }

        return true;
    }

    protected function setSessionAndRecaller(array $data)
    {
        if ($this->setUserSession($this->account)) {
            $newSession['account_id'] = $this->account['id'];
            $newSession['app'] = $this->getKey();
            $newSession['session_id'] = $this->session->getId();
            //Set Session Timeouts
            $newSession['session_idle_timeout'] = time() + (int) $this->config->timeout->session_idle;
            $newSession['session_absolute_timeout'] = time() + (int) $this->config->timeout->session_absolute;

            if ($this->config->databasetype === 'db') {
                $sessionModel = new BasepackagesUsersAccountsSessions;

                $sessionModel->assign($newSession);

                try {
                    $sessionModel->create();
                } catch (\Exception $e) {
                    $this->logger->log->debug('Duplicate session Id Found. This happens when session was deleted from server and browser used an old session ID.');

                    $this->logout(true);

                    throw $e;
                }
            } else {
                $sessionStore = $this->ff->store('basepackages_users_accounts_sessions');

                try {
                    $sessionStore->insert($newSession);
                } catch (\Exception $e) {
                    $this->logger->log->debug('Duplicate session Id Found. This happens when session was deleted from server and browser used an old session ID.');

                    //Delete the duplicate entry and try again.
                    try {
                        //
                    } catch (\Exception $e) {
                        $this->logout(true);

                        throw $e;
                    }
                }
            }
        }

        if (isset($data['remember']) && $data['remember'] == 'true') {
            $this->setRecaller();
        }
    }

    protected function updateSessionIdleTimer(array $session)
    {
        $session['session_idle_timeout'] = time() + (int) $this->config->timeout->session_idle;

        if ($this->config->databasetype === 'db') {
            $sessionModel = new BasepackagesUsersAccountsSessions;

            $sessionModel->assign($session);

            try {
                $sessionModel->update();
            } catch (\Exception $e) {
                $this->logger->log->debug('Not able to update session idle timeout for session id: ' . $session['session_id']);

                $this->logout(true);

                return false;
            }
        } else {
            $sessionStore = $this->ff->store('basepackages_users_accounts_sessions');

            try {
                $sessionStore->update($session);
            } catch (\Exception $e) {
                $this->logger->log->debug('Not able to update session idle timeout for session id: ' . $session['session_id']);

                $this->logout(true);

                return false;
            }
        }

        return true;
    }

    protected function setUserIdCooikie()
    {
        $this->cookies->useEncryption(false);

        $this->cookies->set(
            'id',
            $this->account['id'],
            time() + $this->config->timeout->cookies,
            '/',
            $this->config->dev === true ? false : true,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->send();

        $this->cookies->useEncryption(true);
    }

    protected function clearRecallerCookies()
    {
        //Set cookies to 1 second so browser removes them.
        $this->cookies->set(
            $this->cookieKey,
            '0',
            1,
            '/',
            $this->config->dev === true ? false : true,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->set(
            'id',
            '0',
            1,
            '/',
            $this->config->dev === true ? false : true,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->send();

        if ($this->cookies->has($this->cookieKey)) {
            $this->cookies->delete($this->cookieKey);
        }
    }

    protected function setRecallerCooikie($identifier, $token)
    {
        $this->cookies->set(
            $this->cookieKey,
            $identifier . $this->separator . $token,
            time() + $this->config->timeout->cookies,
            '/',
            $this->config->dev === true ? false : true,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);
    }

    public function setUserFromRecaller()
    {
        list($identifier, $token) = explode($this->separator, $this->cookies->get($this->cookieKey)->getValue());

        $hasIdentifier = $this->basepackages->accounts->hasIdentifier($this->app['route'], $identifier);

        if (!$this->secTools->checkPassword($token, $hasIdentifier['token'])) {
            $this->clearAccountRecaller($this->cookieKey);

            $this->cookies->delete($this->cookieKey);

            $this->logger->log->debug(
                'Cannot set account : ' . $this->account['email'] . ' via cookie for app: ' . $this->app['name']
            );

            throw new \Exception('Cannot set account from cookie');
        }

        $account = $this->basepackages->accounts->getAccountById($hasIdentifier['account_id']);

        if ($account) {
            $this->updateSessionIdForSessionAndIdentifier($hasIdentifier);

            $this->setUserSession($account);

            $this->account = $account;

            return true;
        }

        return false;
    }

    public function getOldSessionId()
    {
        return $this->oldSessionId;
    }

    //Old session expired in browser, update session ids in db, else we will get stale entry in db during logout.
    protected function updateSessionIdForSessionAndIdentifier($identifier)
    {
        $this->oldSessionId = $identifier['session_id'];

        $identifierModel = new BasepackagesUsersAccountsIdentifiers;
        $identifierStore = $this->ff->store($identifierModel->getSource());
        $sessionModel = new BasepackagesUsersAccountsSessions;
        $sessionStore = $this->ff->store($sessionModel->getSource());

        if ($this->oldSessionId !== $this->session->getId()) {
            $identifier['session_id'] = $this->session->getId();

            if ($this->config->databasetype === 'db') {
                $identifierModel->assign($identifier);
                $identifierModel->update();
            } else {
                $identifierStore->update($identifier);
            }

            if ($this->config->databasetype === 'db') {
                $session = $sessionModel::findFirst(
                    [
                        'session_id = :sessionId:',
                        'bind'      => ['sessionId' => $this->oldSessionId]
                    ]
                );

                if ($session) {
                    $session = $session->toArray();
                }
            } else {
                $session = $sessionStore->findOneBy(['session_id', '=', $this->oldSessionId]);
            }

            if ($session) {
                $session['session_id'] = $this->session->getId();

                $this->updateSessionIdleTimer($session);
            } else {
                $this->logger->log->debug('Old session ID for the identifier does not match the new session ID, forcing logout');

                throw new \Exception('Error: Contact System Administrator');
            }
        }
    }

    public function hasRecaller()
    {
        if (!$this->cookies->has('id') && $this->hasUserInSession()) {
            $this->setUserIdCooikie();
        }

        if (!$this->cookies->has($this->cookieKey) && $this->hasUserInSession()) {
            if ($this->config->databasetype === 'db') {
                $identifierModel = new BasepackagesUsersAccountsIdentifiers;

                $identifier = $identifierModel::findFirst(
                    [
                        'session_id = :sessionId:',
                        'bind'      => ['sessionId' => $this->session->getId()]
                    ]
                );

                if ($identifier) {
                    if (!$identifier->delete()) {
                        $this->logger->log->debug($identifier->getMessages());

                        return false;
                    }

                    $this->setRecaller();

                    return true;
                }
            } else {
                $identifierStore = $this->ff->store('basepackages_users_accounts_identifiers');

                $identifier = $identifierStore->findOneBy(['session_id', '=', $this->session->getId()]);

                if ($identifier) {
                    $identifierStore->deleteById($identifier['id']);

                    $this->setRecaller();

                    return true;
                }
            }

            return false;
        }

        return $this->cookies->has($this->cookieKey);
    }

    protected function setRecaller()
    {
        list($identifier, $token) = $this->generateRecaller();

        $newIdentifier['account_id'] = $this->account['id'];
        $newIdentifier['app'] = $this->getKey();
        $newIdentifier['session_id'] = $this->session->getId();
        $newIdentifier['identifier'] = $identifier;
        $newIdentifier['token'] = $this->secTools->hashPassword($token, $this->config->security->cookiesWorkFactor);

        if ($this->config->databasetype === 'db') {
            $identifierModel = new BasepackagesUsersAccountsIdentifiers;

            $identifierModel->assign($newIdentifier);

            $identifierModel->create();
        } else {
            $identifierStore = $this->ff->store('basepackages_users_accounts_identifiers');

            $identifierStore->insert($newIdentifier);
        }

        $this->setRecallerCooikie($identifier, $token);
    }

    protected function generateRecaller()
    {
        return [bin2hex($this->secTools->random->bytes()), bin2hex($this->secTools->random->bytes())];
    }

    public function account()
    {
        return $this->account;
    }

    public function check($resetCache = false)
    {
        if (!$resetCache && $this->account) {
            return true;
        }

        if ($this->hasUserInSession()) {
            $this->access->auth->setUserFromSession();

            return true;
        }

        if ($this->hasRecaller()) {
            $this->access->auth->setUserFromRecaller();

            return true;
        }

        return false;
    }

    public function hasUserInSession()
    {
        return $this->session->has($this->getKey());
    }

    protected function getKey()
    {
        if (!$this->key) {
            $this->setKey();
        }

        return $this->key;
    }

    protected function setKey()
    {
        $this->key = $this->core->core['settings']['security']['sso'] == 'true' ? '*' : strtolower($this->app['route']);
    }

    public function setUserFromSession()
    {
        if ($this->session->get($this->getKey())) {
            $account = $this->basepackages->accounts->getAccountById($this->session->get($this->getKey()));

            if (!$account) {
                $this->logger->log->debug('User not found for app: ' . $this->app['name']);

                throw new \Exception('Error: Contact System Administrator');
            }

            if ($account['sessions'] && is_array($account['sessions']) && count($account['sessions']) > 0) {
                foreach ($account['sessions'] as $session) {
                    if ($session['session_id'] === $this->session->getId() &&
                        $session['app'] === $this->getKey()
                    ) {
                        //Check Timeout
                        if (isset($session['session_absolute_timeout']) && $session['session_absolute_timeout'] > 0) {
                            if (time() > $session['session_absolute_timeout']) {
                                $this->account = $account;

                                $this->logger->log->debug($account['email'] . ' absolute session timeout reached, forcing logout');

                                throw new \Exception('Error: Absolute session timeout!');
                            }
                        }

                        if (isset($session['session_idle_timeout']) && $session['session_idle_timeout'] > 0) {
                            if (time() > $session['session_idle_timeout']) {
                                $this->account = $account;

                                $this->logger->log->debug($account['email'] . ' idle session timeout reached, forcing logout');

                                throw new \Exception('Error: Idle session timeout!');
                            } else {
                                if (!$this->updateSessionIdleTimer($session)) {
                                    throw new \Exception('Error: Contact System Administrator');
                                }
                            }
                        }

                        $this->account = $account;

                        return true;
                    }
                }
            }

            if ($this->cookies->has($this->cookieKey)) {
                return false;
            }

            $this->logger->log->debug(
                $account['email'] . ' session id ' . $this->session->getId() .
                ' not present in DB. Possibly session deleted by administrator via force logout'
            );

            throw new \Exception('Error: Contact System Administrator');
        } else {
            return false;
        }
    }

    protected function setUserSession($account)
    {
        $this->session->set($this->getKey(), $account['id']);

        return true;
    }

    public function validateData(array $data, $task)
    {
        if ($task === 'auth' || $task === 'auth2fa') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
            if ($task === 'auth2fa') {
                if (isset($this->core->core['settings']['security']['twofa']) &&
                    $this->core->core['settings']['security']['twofa'] == 'true' &&
                    isset($this->app['enforce_2fa']) &&
                    $this->app['enforce_2fa'] == '1'
                ) {
                    $this->validation->add('twofa_using', PresenceOf::class, ["message" => "Error! Please contact administrator."]);
                    $this->validation->add('code', PresenceOf::class, ["message" => "Enter valid 2FA code"]);
                    if (isset($data['twofa_using'])) {
                        if ($data['twofa_using'] === 'otp') {
                            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'])) {
                                $this->validation->add('code',
                                                       Min::class,
                                                       [
                                                            "min" => $this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'],
                                                            "message" => "Error: Enter valid 2FA code.",
                                                            "included" => false
                                                        ]
                                                    );
                            }
                        } else if ($data['twofa_using'] === 'email') {
                            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'])) {
                                $this->validation->add('code',
                                                       Min::class,
                                                       [
                                                            "min" => $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'],
                                                            "message" => "Error: Enter valid 2FA code.",
                                                            "included" => false
                                                        ]
                                                    );
                            }
                        }
                    }
                }
            }
        } else if ($task === 'auth2faEmail') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
        } else if ($task === 'forgot') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
        } else if ($task === 'reset') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
            $this->validation->add('newpass', PresenceOf::class, ["message" => "Enter valid new password."]);
            $this->validation->add('confirmnewpass', PresenceOf::class, ["message" => "Enter valid confirm password."]);
            $this->validation->add('confirmnewpass', Confirmation::class,
                [
                    "message"   => "New password and confirm password don't match.",
                    "with"      => "newpass"
                ]
            );

            if (isset($this->core->core['settings']['security']['twofa']) &&
                $this->core->core['settings']['security']['twofa'] == true &&
                isset($this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa']) &&
                $this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa'] == true
            ) {
                $this->validation->add('twofa_using', PresenceOf::class, ["message" => "Error! Please contact administrator."]);
                $this->validation->add('code', PresenceOf::class, ["message" => "2FA code required."]);
                if (isset($data['twofa_using'])) {
                    if ($data['twofa_using'] === 'otp') {
                        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'])) {
                            $this->validation->add('code',
                                                   Min::class,
                                                   [
                                                        "min" => $this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'],
                                                        "message" => "Enter valid 2FA code.",
                                                        "included" => false
                                                    ]
                                                );
                        }
                    } else if ($data['twofa_using'] === 'email') {
                        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'])) {
                            $this->validation->add('code',
                                                   Min::class,
                                                   [
                                                        "min" => $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'],
                                                        "message" => "Enter valid 2FA code.",
                                                        "included" => false
                                                    ]
                                                );
                        }
                    }
                }
            }
        }

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = '';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    public function getAccountSecurityObject()
    {
        if ($this->config->databasetype === 'db') {
            $accountsObj = $this->basepackages->accounts->getFirst('id', $this->account()['id']);

            return $accountsObj->getSecurity();
        } else {
            if (isset($this->account()['security'])) {
                return (object) $this->account()['security'];
            } else {
                $securityStore = $accountsObj->changeStore('basepackages_users_accounts_security');

                $securityStore->findOneBy(['account_id', '=', $this->account()['id']]);

                if ($securityStore->toArray()) {
                    return (object) $securityStore->toArray();
                }
            }
        }
    }
}