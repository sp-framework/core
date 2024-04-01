<?php

namespace System\Base\Providers\AccessServiceProvider;

use OTPHP\HOTP;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Phalcon\Filter\Validation\Validator\Confirmation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Auth
{
    protected $key = null;

    protected $separator = '|';

    protected $request;

    protected $config;

    protected $session;

    protected $oldSessionId;

    protected $sessionTools;

    protected $cookies;

    protected $cookieKey;

    protected $accounts;

    protected $account = null;

    protected $apps;

    protected $app;

    protected $secTools;

    protected $validation;

    protected $logger;

    protected $links;

    protected $profile;

    protected $roles;

    protected $email;

    protected $emailQueue;

    protected $domains;

    protected $ff;

    protected $core;

    protected $basepackages;

    protected $helper;

    public $packagesData;

    public $agent;

    public $blackWhiteList;

    protected $otp;

    protected $cookieTimeout = 0;

    protected $passwordPolicyErrors = [];

    public function __construct(
        $request,
        $config,
        $session,
        $sessionTools,
        $cookies,
        $apps,
        $secTools,
        $validation,
        $logger,
        $links,
        $accounts,
        $profile,
        $roles,
        $email,
        $emailQueue,
        $domains,
        $ff,
        $core,
        $basepackages,
        $helper
    ) {
        $this->request = $request;

        $this->config = $config;

        $this->session = $session;

        $this->sessionTools = $sessionTools;

        $this->cookies = $cookies;

        $this->apps = $apps;

        $this->app = $apps->getAppInfo();

        $this->secTools = $secTools;

        $this->validation = $validation;

        $this->logger = $logger;

        $this->links = $links;

        $this->accounts = $accounts;

        $this->profile = $profile;

        $this->roles = $roles;

        $this->email = $email;

        $this->emailQueue = $emailQueue;

        $this->domains = $domains;

        $this->ff = $ff;

        $this->core = $core;

        $this->basepackages = $basepackages;

        $this->helper = $helper;

        $this->packagesData = new PackagesData;
    }

    public function init()
    {
        $this->cookieKey = 'remember_' . $this->getKey();

        $this->cookieTimeout = time() + $this->config->timeout->cookies;

        return $this;
    }

    public function logout()
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

        if ($this->cookies->has($this->cookieKey)) {
            $this->cookies->delete($this->cookieKey);
        }

        if ($this->session->has('_PHCOOKIE_' . $this->cookieKey)) {
            $this->session->remove('_PHCOOKIE_' . $this->cookieKey);
        }

        if ($this->session->has($this->key)) {
            $this->session->remove($this->key);
        }

        $this->session->redirectUrl = '/';

        $this->packagesData->redirectUrl = $this->links->url('/');

        $this->logger->log->debug($this->account['email'] . ' logged out successfully from app: ' . $this->app['name']);

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

        //Set cookies to 1 second so browser removes them.
        $this->cookies->set(
            $this->cookieKey,
            '0',
            1,
            '/',
            null,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->set(
            'id',
            '0',
            1,
            '/',
            null,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->send();

        if ($this->cookies->has($this->cookieKey)) {
            $this->cookies->delete($this->cookieKey);
        }
    }

    protected function clearAccountSessionId()
    {
        $sessionModel = new BasepackagesUsersAccountsSessions;
        $sessionStore = $this->ff->store($sessionModel->getSource());

        if ($this->config->databasetype === 'db') {
            $session = $sessionModel::findFirst(
                [
                'session_id = :sessionId: AND app = :app:',
                'bind'      =>
                    [
                        'sessionId' => $this->session->getId(),
                        'app'       => $this->getKey()
                    ]
                ]
            );

            if ($session) {
                if (!$session->delete()) {
                    $this->logger->log->debug($session->getMessages());
                }
            }
        } else {
            $sessionStore->findOneBy([['session_id', '=', $this->session->getId()], "AND", ['app', '=', $this->getKey()]]);

            if ($sessionStore->toArray()) {
                $sessionStore->deleteById($sessionStore->toArray()['id']);
            }
        }

        $this->sessionTools->removeSessionKey($this->getKey());
    }

    public function attempt($data)
    {
        $validate = $this->validateData($data, 'auth');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $this->apps->ipFilter->removeFromMonitoring();

        $security = $this->getAccountSecurityObject();

        if (!$this->validateTwoFa($security, $data)) {
            return false;
        }

        $this->addResponse('Authenticated. Redirecting...');

        if (($this->account['security']['force_pwreset'] &&
             $this->account['security']['force_pwreset'] == '1') ||
            (isset($this->account['security']['force_pwreset_after']) &&
             $this->account['security']['force_pwreset_after'] !== null &&
             $this->account['security']['force_pwreset_after'] != '0' &&
             time() > $this->account['security']['force_pwreset_after']
            )
        ) {
            $this->packagesData->redirectUrl = $this->links->url('auth/q/pwreset/true');

            return true;
        }

        if ($this->secTools->passwordNeedsRehash($this->account['security']['password'])) {
            $this->account['security']['password'] = $this->secTools->hashPassword($data['pass'], $this->config->security->passwordWorkFactor);
            $this->accounts->addUpdateSecurity($this->account['id'], $this->account['security']);
        }

        $this->setSessionAndRecaller($data);

        if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
            $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
        } else {
            $this->packagesData->redirectUrl = $this->links->url('home');
        }

        $this->logger->log->debug($this->account['email'] . ' authenticated successfully on app ' . $this->app['name']);

        return true;
    }

    protected function checkAccount(array $data, $viaProfile = null)
    {
        $this->account = $this->accounts->checkAccount($data['user'], true);

        if ($this->account) {
            if ($this->account['status'] != '1') {
                $this->addResponse('Error: Username/Password incorrect!', 1);

                $this->logger->log->debug($data['user'] . ' is disabled!');

                return false;
            }

            //New App OR New account via rego
            $canLogin = $this->accounts->canLogin($this->account['id'], $this->app['id']);

            if ($canLogin === false) {
                if ($this->app['can_login_role_ids']) {
                    if (is_string($this->app['can_login_role_ids'])) {
                        $this->app['can_login_role_ids'] = $this->helper->decode($this->app['can_login_role_ids'], true);
                    }

                    if (in_array($this->account['security']['role_id'], $this->app['can_login_role_ids'])) {
                        if ($this->config->databasetype === 'db') {
                            $canloginModel = new BasepackagesUsersAccountsCanlogin;

                            $newLogin['account_id'] = $this->account['id'];
                            $newLogin['app_id'] = $this->app['id'];
                            $newLogin['allowed'] = '2';

                            $canloginModel->assign($newLogin);

                            $canloginModel->create();
                        } else {
                            $canloginStore = $this->ff->store('basepackages_users_accounts_canlogin');

                            $canloginStore->insert(
                                [
                                    'account_id'    => $this->account['id'],
                                    'app_id'        => $this->app['id'],
                                    'allowed'       => 2
                                ]
                            );
                        }
                    } else {
                        $this->addResponse('Error: Contact System Administrator', 1);

                        $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                        return false;
                    }
                } else {
                    $this->addResponse('Error: Contact System Administrator', 1);

                    $this->logger->log->debug('App\'s can_login_role_ids not set for app ' . $this->app['name']);

                    return false;
                }
            } else if ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '0') {
                $this->addResponse('Error: Contact System Administrator', 1);

                $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                return false;
            }

            if (!$this->secTools->checkPassword($data['pass'], $this->account['security']['password'])) {//Password Fail
                if ($viaProfile) {
                    $this->addResponse('Error: Current Password incorrect!', 1);
                } else {
                    $this->addResponse('Error: Username/Password incorrect!', 1);
                }

                $this->logger->log->debug('Incorrect username/password entered by account ' . $this->account['email'] . ' on app ' . $this->app['name']);

                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the account exists.

            $this->addResponse('Error: Username/Password incorrect!', 1);

            $this->logger->log->debug($data['user'] . ' is not in DB. App: ' . $this->app['name']);

            return false;
        }

        return true;
    }

    protected function validateTwoFa($security, $data)
    {
        if (isset($this->app['enforce_2fa']) && $this->app['enforce_2fa'] == '1') {
            if (isset($data['twofa_using'])) {
                return $this->validateTwoFaCode($security, $data, true);
            }

            $this->addResponse('2FA Code Required!', 3);

            if (in_array('email', $this->core->core['settings']['security']['twofaSettings']) && !$this->email->setup()) {
                unset($this->core->core['settings']['security']['twofaSettings'][array_keys($this->core->core['settings']['security']['twofaSettings'], 'email')[0]]);
            }

            if (count($this->core->core['settings']['security']['twofaSettings']) === 0) {//if otp is not set and email service is not configured, we authenticate.
                return true;
            }

            $this->packagesData->responseData = ['allowed_methods' => $this->core->core['settings']['security']['twofaSettings']];

            if (in_array('otp', $this->core->core['settings']['security']['twofaSettings'])) {
                $this->packagesData->responseData = array_merge($this->packagesData->responseData, ['otp_status' => $security->twofa_otp_status]);
            }

            return false;
        }

        return true;
    }

    protected function validateTwoFaCode($security, $data, $viaLogin = false)
    {
        if (!isset($this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            return true;
        }

        if (!is_array($this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            $this->core->core['settings']['security']['twofaSettings']['twofaUsing'] = $this->helper->decode($this->core->core['settings']['security']['twofaSettings']['twofaUsing'], true);
        }

        if ($data['twofa_using'] === 'email' && in_array('email', $this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            if (time() > $security->twofa_email_code_sent_on + ($this->core->core['settings']['security']['twofaSettings']['twofa_email_timeout'] ?? 60)) {
                $security->twofa_email_code_sent_on = null;
                $security->twofa_email_code = null;

                if ($this->config->databasetype === 'db') {
                    $security->update();
                } else {
                    $securityStore = $this->ff->store('basepackages_users_accounts_security');

                    $securityStore->update((array) $security);
                }

                $this->addResponse('Code Expired! Request new code...', 1);

                return false;
            }

            if ($this->secTools->checkPassword($data['code'], $security->twofa_email_code)) {
                $this->account['security']['twofa_email_code_sent_on'] = null;
                $this->account['security']['twofa_email_code'] = null;

                return true;
            }

            if ($viaLogin) {
                $this->addResponse('Error: Username/Password/2FA Code incorrect!', 1);
            } else {
                $this->addResponse('Error: 2FA Code incorrect!', 1);
            }

            return false;
        } else if ($data['twofa_using'] === 'otp' && in_array('otp', $this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            if ($security->twofa_otp_status == '1' && !isset($data['code'])) {
                $this->addResponse('2FA Code Required', 3);

                return false;
            }

            if ($viaLogin &&
                ($security->twofa_otp_status == '1' && isset($data['code'])) &&
                (isset($security->twofa_otp_secret) && !$this->verifyOtp($data['code'], $security->twofa_otp_secret))
            ) {
                $this->addResponse('Error: Username/Password/2FA Code incorrect!', 1);

                return false;
            } else {
                if (!$security->twofa_otp_status) {
                    $this->addResponse('2FA OTP not enabled. Please enable or use email option (if available)', 1);

                    return false;
                }

                if (isset($security->twofa_otp_secret) &&
                    !$this->verifyOtp($data['code'], $security->twofa_otp_secret)
                ) {
                    $this->addResponse('Error: 2FA Code incorrect!', 1);

                    return false;
                }
            }

            return true;
        }
    }

    protected function setSessionAndRecaller(array $data)
    {
        if ($this->setUserSession()) {
            $newSession['account_id'] = $this->account['id'];
            $newSession['app'] = $this->getKey();
            $newSession['session_id'] = $this->session->getId();

            if ($this->config->databasetype === 'db') {
                $sessionModel = new BasepackagesUsersAccountsSessions;

                $sessionModel->assign($newSession);

                try {
                    $sessionModel->create();
                } catch (\Exception $e) {
                    $this->logger->log->debug('Duplicate session Id Found. This happens when session was deleted from server and browser used an old session ID.');

                    $this->logout();

                    throw $e;
                }
            } else {
                $sessionStore = $this->ff->store('basepackages_users_accounts_sessions');

                try {
                    $sessionStore->insert($newSession);
                } catch (\Exception $e) {
                    $this->logger->log->debug('Duplicate session Id Found. This happens when session was deleted from server and browser used an old session ID.');

                    $this->logout();

                    throw $e;
                }
            }
        }

        $this->setUserIdCooikie();

        if (isset($data['remember']) && $data['remember'] === 'true') {
            $this->setRecaller();
        }
    }

    protected function setUserIdCooikie()
    {
        $this->cookies->useEncryption(false);

        $this->cookies->set(
            'id',
            $this->account['id'],
            $this->cookieTimeout,
            '/',
            null,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->send();

        $this->cookies->useEncryption(true);
    }

    public function setUserFromRecaller()
    {
        list($identifier, $token) = explode($this->separator, $this->cookies->get($this->cookieKey)->getValue());

        $hasIdentifier = $this->accounts->hasIdentifier($this->app['route'], $identifier);

        if (!$this->secTools->checkPassword($token, $hasIdentifier['token'])) {
            $this->clearAccountRecaller($this->cookieKey);

            $this->cookies->delete($this->cookieKey);

            $this->logger->log->debug(
                'Cannot set account : ' . $this->account['email'] . ' via cookie for app: ' . $this->app['name']
            );

            throw new \Exception('Cannot set account from cookie');
        }

        $this->account = $this->accounts->getAccountById($hasIdentifier['account_id']);

        if ($this->account) {
            $this->updateSessionIdForSessionAndIdentifier($hasIdentifier);

            // $this->setAccountProfile();

            // $this->setAccountRole();

            return true;
        }

        return false;
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

                if ($this->config->databasetype === 'db') {
                    $sessionModel->assign($session);

                    $sessionModel->update();
                } else {
                    $sessionStore->update($session);
                }
            }
        }

        $this->setUserSession();
    }

    public function hasRecaller()
    {
        if (!$this->cookies->has($this->cookieKey) && $this->hasUserInSession()) {
            if (!$this->cookies->has('id')) {
                $this->setUserIdCooikie();
            }

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

        $this->cookies->set(
            $this->cookieKey,
            $identifier . $this->separator . $token,
            $this->cookieTimeout,
            '/',
            null,
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->send();

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
    }

    protected function generateRecaller()
    {
        return [bin2hex($this->secTools->random->bytes()), bin2hex($this->secTools->random->bytes())];
    }

    public function account()
    {
        return $this->account;
    }

    public function check()
    {
        if ($this->account) {
            return true;
        }

        if ($this->hasUserInSession() || $this->hasRecaller()) {
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
            $this->account = $this->accounts->getAccountById($this->session->get($this->getKey()));

            if (!$this->account) {
                $this->logger->log->debug($this->account['email'] . ' not found in session for app: ' . $this->app['name']);

                throw new \Exception('User not found in session');
            }

            if ($this->account['sessions'] && is_array($this->account['sessions']) && count($this->account['sessions']) > 0) {
                foreach ($this->account['sessions'] as $session) {
                    if (isset($session['session_id']) &&
                        $session['session_id'] === $this->session->getId() &&
                        $session['app'] === $this->getKey()
                    ) {
                        return true;
                    }
                }
            }

            $this->logger->log->debug($this->account['email'] . ' session id ' . $this->session->getId() . ' not present in DB.');

            $this->sessionTools->clearSession($this->session->getId());

            throw new \Exception('User session deleted in DB by administrator via force logout.');
        } else {
            return false;
        }
    }

    protected function setUserSession()
    {
        $this->session->set($this->getKey(), $this->account['id']);

        return true;
    }

    protected function validateData(array $data, $task)
    {
        if ($task === 'auth') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
        } else if ($task === 'agent') {
            $this->validation->add('code', PresenceOf::class, ["message" => "Enter valid code."]);
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
                $this->validation->add('code', PresenceOf::class, ["message" => "Enter valid 2FA code."]);
            }
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

    public function forgotPassword(array $data)
    {
        $validate = $this->validateData($data, 'forgot');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        $this->account = $this->accounts->checkAccount($data['user'], true);

        if ($this->account) {
            $this->account['force_logout'] = '1';

            $this->account['email_new_password'] = '1';

            $this->account['pwreset_email'] = '1';

            if ($this->accounts->updateAccount($this->account)) {
                $this->logger->log->info('New password requested for account ' . $this->account['email'] . ' via forgot password. New password was emailed to the account.');
            } else {
                $this->logger->log->critical('Trying to send new password for ' . $this->account['email'] . ' via forgot password failed.');
            }
        }

        $this->addResponse('Email Sent. Please follow password reset instructions from the email.');

        return true;
    }

    public function resetPassword(array $data, $viaProfile = null)
    {
        if ($data['pass'] === $data['newpass']) {
            $this->addResponse('Old and new password match!', 1);

            return false;
        }
        $validate = $this->validateData($data, 'reset');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->checkAccount($data, $viaProfile)) {
            return false;
        }

        if (isset($this->core->core['settings']['security']['twofa']) &&
            $this->core->core['settings']['security']['twofa'] == true &&
            isset($this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa']) &&
            $this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa'] == true
        ) {
            if (!$this->validateTwoFaCode($this->getAccountSecurityObject(), $data)) {
                // return false;
            }
        }

        $passwordPolicy = false;
        if (isset($this->core->core['settings']['security']['passwordPolicy']) &&
            $this->core->core['settings']['security']['passwordPolicy'] == 'true'
        ) {
            $passwordPolicy = true;
            $this->passwordPolicyErrors['passwordPolicyBlockPreviousPasswords'] = false;
            $this->passwordPolicyErrors['passwordPolicySimpleAcceptableLevel'] = false;

            if (!$this->checkPwPolicy($data)) {
                $this->addResponse('New password failed password policy. Please try again...', 1, ['passwordPolicyErrors' => $this->passwordPolicyErrors]);

                return false;
            }
        }

        $security = $this->getAccountSecurityObject();

        $security->password = $this->secTools->hashPassword($data['newpass'], $this->config->security->passwordWorkFactor);
        $security->force_pwreset = null;
        $security->password_set_on = time();

        if ($passwordPolicy) {
            $security = $this->setPasswordHistory($data, $security);
        }

        if ($this->accounts->addUpdateSecurity($this->account['id'], (array) $security)) {
            $this->logger->log->info('Password reset successful for account ' . $this->account['email'] . ' via pwreset.');


            if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
                $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
            } else {
                $this->packagesData->redirectUrl = $this->links->url('home');
            }

            if ($viaProfile) {
                $this->addResponse('Password change successful.');

                //Check if we need to relogin or not.
                if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForceReloginAfterPwreset']) &&
                    $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForceReloginAfterPwreset'] == true
                ) {
                    $this->logout();

                    return true;
                }

                unset($this->packagesData->redirectUrl);
                unset($this->packagesData->responseData);
            } else {
                $this->addResponse('Authenticated. Password changed. Redirecting...');
            }

            return true;
        } else {
            $this->addResponse($this->accounts->packagesData->responseMessage, 1);

            return false;
        }
    }

    protected function checkPasswordHistory($data)
    {
        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'] > 0
        ) {
            $security = $this->getAccountSecurityObject();

            if ($security->password_history && $security->password_history !== '') {
                if (is_string($security->password_history)) {
                    $security->password_history = $this->helper->decode($security->password_history, true);
                }

                $security->password_history = array_reverse($security->password_history, true);//reverse to check last password first and so on.

                if (count($security->password_history) > 0) {
                    $count = 1;
                    foreach ($security->password_history as $history) {
                        if ($this->secTools->checkPassword($data['newpass'], $history)) {
                            return true;
                        }

                        if ($count === (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                            return false;//we only check x amount of password configured, rest we ignore.
                        }

                        $count++;
                    }
                }
            }
        }

        return false;
    }

    protected function setPasswordHistory($data, $security)
    {
        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'] > 0
        ) {
            if ($security->password_history && $security->password_history !== '') {
                if (is_string($security->password_history)) {
                    $security->password_history = $this->helper->decode($security->password_history, true);
                }

                if (count($security->password_history) < (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    $security->password_history[$security->password_set_on] = $security->password;
                } else if (count($security->password_history) === (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    //remove oldest password and add the new one.
                    $security->password_history = array_slice($security->password_history, 1, null, true);
                    $security->password_history[$security->password_set_on] = $security->password;
                } else if (count($security->password_history) > (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    $historyLengthShouldBe = count($security->password_history) - (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'];
                    $security->password_history = array_slice($security->password_history, $historyLengthShouldBe + 1, null, true);//remove more then defined + 1 for the last password.
                    $security->password_history[$security->password_set_on] = $security->password;
                }
            } else {
                $security->password_history[$security->password_set_on] = $security->password;
            }
        }

        return $security;
    }

    protected function checkPwPolicy($data)
    {
        if ($this->checkPasswordHistory($data)) {
            $this->passwordPolicyErrors['passwordPolicyBlockPreviousPasswords'] = true;

            return false;
        }

        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'])) {
            if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'] === 'simple') {
                $checkPwStrength = $this->checkPwStrength($data['newpass']);

                if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel']) &&
                    (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel'] > 0
                ) {
                    if ($checkPwStrength !== false &&
                        $checkPwStrength < (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel']
                    ) {
                        $this->passwordPolicyErrors['passwordPolicySimpleAcceptableLevel'] = true;

                        return false;
                    }
                }
            } else if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'] === 'complex') {
                //
            }
        }

        return true;
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new \ZxcvbnPhp\Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            $this->addResponse('Checking Password Strength Success', 0, $result['score']);

            return $result['score'];
        }

        $this->addResponse('Error Checking Password Strength', 1);

        return false;
    }

    public function generateNewPassword()
    {
        $this->basepackages->utils->generateNewPassword();

        $this->addResponse(
            $this->basepackages->utils->packagesData->responseMessage,
            $this->basepackages->utils->packagesData->responseCode,
            $this->basepackages->utils->packagesData->responseData['password']
        );
    }

    protected function initOtp($secret, $verify = false)
    {
        if ((!isset($this->core->core['settings']['security']['twofa']) ||
             !isset($this->core->core['settings']['security']['twofaSettings']['twofaOtp'])) ||
             (isset($this->core->core['settings']['security']['twofa']) &&
              $this->core->core['settings']['security']['twofa'] == 'false'
             )
        ) {
            throw new \Exception('OTP is not configured. Please configure it via Core settings.');
        }

        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'totp') {
            $this->otp = TOTP::create($secret);
        } else if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            $this->otp = HOTP::create($secret);
        }

        $this->otp->setDigest('sha256');

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpAlgorithm'])) {
            $this->otp->setDigest($this->core->core['settings']['security']['twofaSettings']['twofaOtpAlgorithm']);
        }

        $period = 30;

        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'totp') {
            $period = 30;

            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'])) {
                $period =
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'] >= 30 &&
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'] <= 300
                    ?
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout']
                    :
                    30;
            }

            $this->otp->setPeriod($period);
        } else if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            $this->otp->setCounter(0);

            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter'])) {
                $this->otp->setCounter($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter']);
            }

            if ($verify) {
                $security = $this->getAccountSecurityObject();
                if ($security->twofa_otp_hotp_counter !== null) {
                    $this->otp->setCounter($security->twofa_otp_hotp_counter);
                }
            }
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpLabel'])) {
            $this->otp->setLabel($this->core->core['settings']['security']['twofaSettings']['twofaOtpLabel']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpIssuer'])) {
            $this->otp->setIssuer($this->core->core['settings']['security']['twofaSettings']['twofaOtpIssuer']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'])) {
            $this->otp->setDigits($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo']) &&
            $this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo'] !== ''
        ) {
            $logoLink = $this->core->get2faLogoLink($this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo'], 80);

            if ($logoLink) {
                $this->otp->setParameter('image', $logoLink);
            }
        }
    }

    public function enableTwoFaOtp(array $data = null)
    {
        if ($data) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->addResponse($validate, 1);

                return false;
            }
        }

        if ($data && !$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->twofa_otp_status && $security->twofa_otp_status == '1') {
            $this->addResponse('2FA already enabled! Contact Administrator.', 1);

            return false;
        }

        try {
            $this->initOtp($this->updateTwoFaOtpSecret());

            $this->packagesData->provisionUrl = $this->otp->getProvisioningUri();

            $this->packagesData->qrcode =
                $this->basepackages->qrcodes->generateQrCode(
                    $this->otp->getProvisioningUri(),
                    [
                        'showLabel'     => 'true',
                        'labelFontSize' => '8',
                        'labelText'     => $this->otp->getSecret(),
                        'labelColor'    =>
                        [
                            'r'         => '0',
                            'g'         => '0',
                            'b'         => '0',
                            'a'         => '0'
                        ]
                    ]
                );

            $this->packagesData->secret = $this->otp->getSecret();

            $this->addResponse('Generated 2FA Code');

            $security = $this->getAccountSecurityObject();

            $security = $this->updateTwoFaOtpHotpCounter($security);

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            return true;
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }
    }

    protected function updateTwoFaOtpHotpCounter($security)
    {
        //Update user counter
        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            if ($security->twofa_otp_hotp_counter !== null) {
                $security->twofa_otp_hotp_counter = $this->otp->getCounter();
            } else {
                if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter'])) {
                    $security->twofa_otp_hotp_counter = $this->otp->getCounter();
                } else {
                    $security->twofa_otp_hotp_counter = 0;
                }
            }
        }

        return $security;
    }

    public function verifyTwoFaOtp(array $data)
    {
        if (isset($data['user']) && isset($data['pass'])) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->addResponse($validate, 1);

                return false;
            }
        }

        if (isset($data['user']) && isset($data['pass']) && !$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->twofa_otp_status && $security->twofa_otp_status == '1') {
            $this->addResponse('2FA already enabled! Contact Administrator.', 1);

            return false;
        }

        if ($this->verifyOtp($data['code'], $security->twofa_otp_secret)) {
            $security->twofa_otp_status = '1';

            $security = $this->updateTwoFaOtpHotpCounter($security);

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            return true;
        }
    }

    public function disableTwoFaOtp(int $code)
    {
        $security = $this->getAccountSecurityObject();

        try {
            $this->initOtp($security->twofa_otp_secret, true);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->otp->verify($code, null, 5)) {
            $security->twofa_otp_status = null;
            $security->twofa_otp_secret = null;
            $security->twofa_otp_hotp_counter = null;

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            $this->addResponse('2FA disabled.');
        } else {
            $this->addResponse('2FA disable failed.', 1);
        }
    }

    public function verifyOtp($code, $secret)
    {
        try {
            $this->initOtp($secret, true);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->otp->verify($code, null, 5)) {
            $this->addResponse('2FA verification success.');

            return true;
        } else {
            $this->addResponse('2FA verification failed.', 1);

            return false;
        }
    }

    protected function updateTwoFaOtpSecret()
    {
        $secretSize = 16;
        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpSecretSize'])) {
            $secretSize = $this->core->core['settings']['security']['twofaSettings']['twofaOtpSecretSize'];
        }
        $twoFaSecret = strtoupper($this->secTools->random->base62($secretSize));

        $security = $this->getAccountSecurityObject();

        $security->twofa_otp_secret = $twoFaSecret;

        if ($this->config->databasetype === 'db') {
            $security->update();
        } else {
            $securityStore = $this->ff->store('basepackages_users_accounts_security');

            $securityStore->update((array) $security);
        }

        return $twoFaSecret;
    }

    protected function getAccountSecurityObject()
    {
        $accountsObj = $this->accounts->getFirst('id', $this->account()['id']);

        if ($this->config->databasetype === 'db') {
            return $accountsObj->getSecurity();
        } else {
            $account = $accountsObj->toArray();

            if ($account) {
                $securityStore = $accountsObj->changeStore('basepackages_users_accounts_security');

                $securityStore->findOneBy(['account_id', '=', $this->account()['id']]);

                if ($securityStore->toArray()) {
                    return (object) $securityStore->toArray();
                }
            }
        }
    }

    public function checkAgent()
    {
        $update = false;
        $clientAddress = $this->request->getClientAddress();
        $userAgent = $this->request->getUserAgent();
        $sessionId = $this->session->getId();
        $agent = [];

        $this->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->accounts->getFirst('id', $this->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $sessionId,
                                'aid'       => $this->account()['id']
                            ]
                        ]
                    );

                if ($agentObj) {
                    $agent = $agentObj->toArray();
                } else {
                    $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
                }
            } else {
                $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
            }
        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $sessionId], ['account_id', '=', $this->account()['id']]]);

                if (!$agent) {
                    $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
                }
            } else {
                $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
            }
        }

        if ($agent && count($agent) > 0) {
            if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->account()['id'] &&
                $agent['verified'] == '1'
            ) {
                return true;
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->account()['id'] &&
                $agent['verified'] == '0'
            ) {
                if (!$this->email->setup()) {
                    return true;
                }

                return false;
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] !== $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->account()['id'] &&
                $agent['verified'] == '1'
            ) {
                // Browser could have updated causing causing agent information change
                // We will remove the agent entry and ask for reauth, just in case.
                if ($this->config->databasetype === 'db') {
                    $agentObj->delete();
                } else {
                    $agentStore->deleteById($agent['id'], false);
                }

                $this->account['force_logout'] = '1';

                $this->accounts->update($this->account);

                $this->logout();

                return false;
            } else if ($agent['session_id'] === $sessionId &&
                       $agent['verified'] == '1'
            ) {
                $this->logger->log->emergency('Same session being used by another browser! Probably session hijack!');

                $this->account['force_logout'] = '1';

                $this->accounts->update($this->account);

                $this->logout();

                return false;
            }
        }

        //If Email is not configured, we cannot send new passcodes.
        //User has remember Identifier set and sessionID has changed.
        if (!$this->email->setup() || $update === true) {
            return true;
        }

        return false;
    }

    protected function addUpdateAgent($sessionId, $clientAddress, $userAgent)
    {
        if (!$this->email->setup() || $this->oldSessionId) {
            $verified = 1;
        } else {
            $verified = 0;
        }

        $agentsObj = new BasepackagesUsersAccountsAgents;
        $agentsStore = $this->ff->store($agentsObj->getSource());

        $oldAgent = [];

        if ($this->oldSessionId) {
            if ($this->config->databasetype === 'db') {
                $oldAgentObj = $agentsObj->findFirstBysession_id($this->oldSessionId);

                if ($oldAgentObj) {
                    $oldAgent = $oldAgentObj->toArray();
                }
            } else {
                $oldAgent = $agentsStore->findOneBy(['session_id', '=', $this->oldSessionId]);
            }

            if ($oldAgent && count($oldAgent) > 0) {
                $oldAgent['session_id'] = $sessionId;
                $oldAgent['account_id'] = $this->account()['id'];
                $oldAgent['verified'] = $verified;

                try {
                    if ($this->config->databasetype === 'db') {
                        $oldAgentObj->assign($oldAgent);

                        $oldAgentObj->update();
                    } else {
                        $agentsStore->update($oldAgent);
                    }

                    $this->oldSessionId = null;

                    return true;
                } catch (\Exception $e) {
                    $this->logout();

                    throw $e;
                }
            } else {
                $this->oldSessionId = null;

                return $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
            }
        } else {
            $newAgent =
                [
                    'account_id'        => $this->account['id'],
                    'session_id'        => $sessionId,
                    'client_address'    => $clientAddress,
                    'user_agent'        => $userAgent,
                    'verified'          => $verified
                ];

            try {
                if ($this->config->databasetype === 'db') {
                    $agentsObj->assign($newAgent);

                    $agentsObj->create();
                } else {
                    $agentsStore->insert($newAgent);
                }

                return false;
            } catch (\Exception $e) {
                $this->logout();

                throw $e;
            }
        }
    }

    public function sendVerificationEmail()
    {
        if (!$this->account) {
            $this->setUserFromSession();
        }

        $this->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->accounts->getFirst('id', $this->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $this->session->getId(),
                                'aid'       => $this->account()['id']
                            ]
                        ]
                    );

                $agent = $agentObj->toArray();
            }
        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $this->session->getId()], ['account_id', '=', $this->account()['id']]]);
            }
        }
        if (isset($agent['email_code_sent_on'])) {
            if (time() < $agent['email_code_sent_on'] + ($this->core->core['settings']['security']['agent_email_timeout'] ?? 60)) {
                $this->addResponse(
                    'Email already sent, please wait...',
                    1,
                    [
                        'code_sent_on' => $agent['email_code_sent_on'],
                        'email_timeout' => $this->core->core['settings']['security']['agent_email_timeout'] ?? 60
                    ]
                );

                return false;
            }

            $agent['email_code_sent_on'] = time();
        } else {
            $agent['email_code_sent_on'] = time();
        }

        $code = $this->secTools->random->base62(12);

        $agent['verification_code'] = $this->secTools->hashPassword($code, $this->config->security->passwordWorkFactor);

        if ($this->config->databasetype === 'db') {
            $agentObj->assign($agent)->update();
        } else {
            $agentStore->update($agent);
        }

        if ($this->emailVerificationCode($code)) {
            $this->logger->log
                ->info('New verification code requested for account ' .
                       $this->account['email'] .
                       ' via authentication agent. New code was emailed to the account.'
                );

            $this->addResponse('Email Sent!', 0, ['email_timeout' => $this->core->core['settings']['security']['agent_email_timeout'] ?? 60]);

            return;
        }

        $this->addResponse('Please contact administrator.', 1);

        $this->packagesData->redirectUrl = $this->links->url('auth');
    }

    protected function emailVerificationCode($verificationCode)
    {
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$this->account['email']]);
        $emailData['subject'] = 'Agent verification code for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $verificationCode;

        return $this->emailQueue->addToQueue($emailData);
    }

    public function verifyVerficationCode(array $data)
    {
        $validate = $this->validateData($data, 'agent');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->account) {
            $this->setUserFromSession();
        }

        $clientAddress = $this->request->getClientAddress();
        $userAgent = $this->request->getUserAgent();
        $sessionId = $this->session->getId();

        $this->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->accounts->getFirst('id', $this->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $sessionId,
                                'aid'       => $this->account()['id']
                            ]
                        ]
                    );
            } else {
                $this->addResponse('Please contact administrator.', 1);

                return;
            }
            $agent = $agentObj->toArray();

        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $sessionId], ['account_id', '=', $this->account()['id']]]);
            }
        }

        if (time() > $agent['email_code_sent_on'] + ($this->core->core['settings']['security']['agent_email_timeout'] ?? 60)) {
            $agent['email_code_sent_on'] = null;
            $agent['verification_code'] = null;

            if ($this->config->databasetype === 'db') {
                $agentObj->assign($agent)->update();
            } else {
                $agentStore->update($agent);
            }

            $this->addResponse('Code Expired! Request new code...', 1);

            return false;
        }

        if ($this->secTools->checkPassword($data['code'], $agent['verification_code'])) {
            if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->account()['id'] &&
                $agent['verified'] == '0'
            ) {
                if ($this->config->databasetype === 'db') {
                    $agentObj->assign(['verified' => '1', 'verification_code' => null])->update();
                } else {
                    $agent['verified'] = 1;
                    $agent['verification_code'] = null;
                    $agent['email_code_sent_on'] = null;

                    $agentStore->update($agent);
                }

                $this->addResponse('Authenticated. Redirecting...');

                if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
                    $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
                } else {
                    $this->packagesData->redirectUrl = $this->links->url('home');
                }
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['account_id'] === $this->account()['id'] &&
                $agent['session_id'] === $sessionId &&
                $agent['verified'] == '1'
            ) {
                $this->addResponse('Session Incorrect... Loggin out.', 1);

                $this->logout();
            }
        } else {
            $this->addResponse('Incorrect verification code. Try again.', 1);
        }
    }

    public function sendTwoFaEmail(array $data)
    {
        $validate = $this->validateData($data, 'auth');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->account) {
            $this->checkAccount($data);
        }

        $codeLength = 12;
        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'])) {
            $codeLength = (int) $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'];
        }

        $code = $this->secTools->random->base62($codeLength);

        $security = $this->getAccountSecurityObject();

        if (isset($security->twofa_email_code_sent_on)) {
            if (time() < $security->twofa_email_code_sent_on + ($this->core->core['settings']['security']['twofaSettings']['twofa_email_timeout'] ?? 60)
            ) {
                $this->addResponse(
                    'Email already sent, please wait to send another code...',
                    1,
                    [
                        'code_sent_on' => $security->twofa_email_code_sent_on,
                        'email_timeout' => $this->core->core['settings']['security']['twofaSettings']['twofa_email_timeout'] ?? 60
                    ]
                );

                return false;
            }

            $security->twofa_email_code_sent_on = time();
        } else {
            $security->twofa_email_code_sent_on = time();
        }

        $security->twofa_email_code = $this->secTools->hashPassword($code, $this->config->security->passwordWorkFactor);

        if ($this->config->databasetype === 'db') {
            $security->update();
        } else {
            $securityStore = $this->ff->store('basepackages_users_accounts_security');

            $securityStore->update((array) $security);
        }

        if ($this->emailTwoFaEmailCode($code)) {
            $this->logger->log
                ->info('New 2FA code requested for account ' .
                       $this->account['email'] .
                       ' via authentication. New code was emailed to the account.'
                );

            $this->addResponse(
                'Email Sent!',
                0,
                ['email_timeout' => $this->core->core['settings']['security']['twofaSettings']['twofa_email_timeout'] ?? 60]
            );

            return;
        }

        $this->addResponse('Please contact administrator.', 1);

        $this->packagesData->redirectUrl = $this->links->url('auth');
    }

    protected function emailTwoFaEmailCode($twofaCode)
    {
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$this->account['email']]);
        $emailData['subject'] = '2FA code for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $twofaCode;

        return $this->emailQueue->addToQueue($emailData);
    }

    protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null)
    {
        $this->packagesData->responseMessage = $responseMessage;

        $this->packagesData->responseCode = $responseCode;

        if ($responseData !== null) {
            $this->packagesData->responseData = $responseData;
        }
    }
}