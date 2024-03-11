<?php

namespace System\Base\Providers\AccessServiceProvider;

use Apps\Core\Packages\System\Tools\Qrcodes\Qrcodes;
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

    public $packagesData;

    public $agent;

    public $blackWhiteList;

    protected $cookieTimeout = 0;

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
        $ff
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

        $this->packagesData = new PackagesData;
    }

    public function init()
    {
        $this->cookieKey = 'remember_' . $this->getKey();

        $this->cookieTimeout = time() + 86400;//Get this time from configuration

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
            null,
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->set(
            'id',
            '0',
            1,
            '/',
            null,
            null,
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
                'session_id = :sessionId:',
                'bind'      => ['sessionId' => $this->session->getId()]
                ]
            );

            if ($session) {
                if (!$session->delete()) {
                    $this->logger->log->debug($session->getMessages());
                }
            }
        } else {
            $sessionStore->findOneBy(['session_id', '=', $this->session->getId()]);

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
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

            return false;
        }

        if (!$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $this->apps->ipFilter->removeFromMonitoring();

        $security = $this->getAccountSecurityObject();

        if (isset($this->app['enforce_2fa']) && $this->app['enforce_2fa'] == '1') {
            if (!$security->two_fa_status ||
                ($security->two_fa_status && $security->two_fa_status == '0')
            ) {
                $this->packagesData->responseCode = 3;

                $this->packagesData->responseMessage = '2FA Code Required!';

                $this->packagesData->redirectUrl = $this->links->url('auth/q/setup2fa/true');

                return true;
            }
        }

        if ($security->two_fa_status == '1' && !isset($data['code'])) {
            $this->packagesData->responseCode = 3;

            $this->packagesData->responseMessage = '2FA Code Required';

            return false;
        }

        if (($security->two_fa_status == '1' && isset($data['code'])) &&
            (isset($security->two_fa_secret) && !$this->verifyTwoFa($data['code'], $security->two_fa_secret))
        ) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password/2FA Code incorrect!';

            return false;
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

        if ($this->account['security']['force_pwreset'] && $this->account['security']['force_pwreset'] == '1') {

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
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

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
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                        $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                        return false;
                    }
                } else {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                    $this->logger->log->debug('App\'s can_login_role_ids not set for app ' . $this->app['name']);

                    return false;
                }
            } else if ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '0') {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                return false;
            }

            if (!$this->secTools->checkPassword($data['pass'], $this->account['security']['password'])) {//Password Fail
                $this->packagesData->responseCode = 1;

                if ($viaProfile) {
                    $this->packagesData->responseMessage = 'Error: Current Password incorrect!';
                } else {
                    $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';
                }

                $this->logger->log->debug('Incorrect username/password entered by account ' . $this->account['email'] . ' on app ' . $this->app['name']);

                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the account exists.

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

            $this->logger->log->debug($data['user'] . ' is not in DB. App: ' . $this->app['name']);

            return false;
        }

        return true;
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
            null,
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
            null,
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
        $this->key = strtolower($this->app['route']);
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
                    if (isset($session['session_id']) && $session['session_id'] === $this->session->getId()) {
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
        } else if ($task === 'forgot') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
        } else if ($task === 'reset') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
            $this->validation->add('newpass', PresenceOf::class, ["message" => "Enter valid new password."]);
            $this->validation->add('confirmnewpass', Confirmation::class,
                [
                    "message"   => "New password and confirm password don't match.",
                    "with"      => "newpass"
                ]
            );
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
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

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

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Email Sent. Please follow password reset instructions from the email.';

        return true;
    }

    public function resetPassword(array $data, $viaProfile = null)
    {
        $validate = $this->validateData($data, 'reset');

        if ($validate !== true) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

            return false;
        }

        if (!$this->checkAccount($data, $viaProfile)) {
            return false;
        }

        if ($this->config->dev === false) {
            $checkPwStrength = $this->checkPwStrength($data['newpass']);

            if ($checkPwStrength !== false && $checkPwStrength < 4) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Password strength is too low.';

                return false;
            }
        }

        $this->account['security']['password'] = $this->secTools->hashPassword($data['newpass'], $this->config->security->passwordWorkFactor);

        $this->account['security']['force_pwreset'] = null;

        if ($this->accounts->addUpdateSecurity($this->account['id'], $this->account['security'])) {
            $this->logger->log->info('Password reset successful for account ' . $this->account['email'] . ' via pwreset.');

            $this->packagesData->responseCode = 0;

            if ($viaProfile) {
                $this->logout();
            } else {
                $this->packagesData->responseMessage = 'Authenticated. Password changed. Redirecting...';
            }

            if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
                $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
            } else {
                $this->packagesData->redirectUrl = $this->links->url('home');
            }

            return true;
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $this->accounts->packagesData->responseMessage;

            return false;
        }
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new \ZxcvbnPhp\Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseData = $result['score'];

            $this->packagesData->responseMessage = 'Checking Password Strength Success';

            return $result['score'];
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Error Checking Password Strength';

        return false;
    }

    public function generateNewPassword()
    {
        $this->packagesData->responseCode = 0;

        $this->packagesData->responseData = $this->secTools->random->base62(12);

        $this->packagesData->responseMessage = 'Password Generate Successfully';
    }

    public function enableTwoFa(array $data = null)
    {
        if ($data) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $validate;

                return false;
            }
        }

        if ($data && !$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->two_fa_status && $security->two_fa_status == '1') {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA already enabled! Contact Administrator.";

            return false;
        }

        try {
            $totp = TOTP::create($this->updateTwoFaSecret());

            $totp->setLabel($this->account['email']);

            $totp->setIssuer('Bazaari');

            $qrCodesPackage = new Qrcodes();

            $this->packagesData->provisionUrl = $totp->getProvisioningUri();

            $this->packagesData->qrcode =
                $qrCodesPackage->generateQrCode(
                    $totp->getProvisioningUri(),
                    [
                        'showLabel'     => 'true',
                        'labelFontSize' => '8',
                        'labelText'     => $totp->getSecret(),
                        'labelColor'    =>
                        [
                            'r'         => '0',
                            'g'         => '0',
                            'b'         => '0',
                            'a'         => '0'
                        ]
                    ]
                );

            $this->packagesData->secret = $totp->getSecret();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Generated 2FA Code';

            return true;
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            return false;
        }
    }

    public function enableVerifyTwoFa(array $data)
    {
        if (isset($data['user']) && isset($data['pass'])) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $validate;

                return false;
            }
        }

        if (isset($data['user']) && isset($data['pass']) && !$this->checkAccount($data)) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->two_fa_status && $security->two_fa_status == '1') {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA already enabled! Contact Administrator.";

            return false;
        }

        if ($this->verifyTwoFa($data['code'], $security->two_fa_secret)) {
            $security->two_fa_status = '1';

            $security->update();

            return true;
        }
    }

    public function disableTwoFa(int $code)
    {
        $security = $this->getAccountSecurityObject();

        $totp = TOTP::create($security->two_fa_secret);

        if ($totp->verify($code)) {
            $security->two_fa_status = null;

            $security->two_fa_secret = null;

            $security->update();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = "2FA disabled.";
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA disable failed.";
        }
    }

    public function verifyTwoFa(int $code, $secret)
    {
        $totp = TOTP::create($secret);

        if ($totp->verify($code)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = "2FA verification success.";

            return true;
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA verification failed.";

            return false;
        }
    }

    protected function updateTwoFaSecret()
    {
        $twoFaSecret = trim(Base32::encodeUpper(random_bytes(16)), '=');

        $security = $this->getAccountSecurityObject();

        if ($this->config->databasetype === 'db') {
            $security->two_fa_secret = $twoFaSecret;

            $security->update();
        } else {
            //
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

            $this->packagesData->responseMessage = 'Email Sent!';

            $this->packagesData->responseCode = 0;

            return;
        }

        $this->packagesData->redirectUrl = $this->links->url('auth');

        $this->packagesData->responseMessage = 'Please contact administrator.';

        $this->packagesData->responseCode = 1;
    }

    protected function emailVerificationCode($verificationCode)
    {
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$this->account['email']]);
        $emailData['subject'] = 'Verification Code for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $verificationCode;

        return $this->emailQueue->addToQueue($emailData);
    }

    public function verifyVerficationCode(array $data)
    {
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
                $this->packagesData->responseMessage = 'Please contact administrator.';

                $this->packagesData->responseCode = 1;

                return;
            }
            $agent = $agentObj->toArray();

        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $sessionId], ['account_id', '=', $this->account()['id']]]);
            }
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

                    $agentStore->update($agent);
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

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
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Session Incorrect... Loggin out.';

                $this->logout();
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Incorrect verification code. Try again.';
        }
    }
}