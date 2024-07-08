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

    protected $cookieTimeout = 0;

    public function init()
    {
        $this->app = $this->apps->getAppInfo();

        $this->cookieKey = 'remember_' . $this->getKey();

        $this->cookieTimeout = time() + $this->config->timeout->cookies;

        $this->twoFa = new TwoFa();

        $this->password = new Password();

        return $this;
    }

    public function attempt($data)
    {
        $validate = $this->validateData($data, 'auth');

        if ($validate !== true) {
            if (str_contains(strtolower($validate), '2fa code')) {
                if (str_contains(strtolower($validate), 'please contact administrator')) {
                    $validate = str_replace('Error! Please contact administrator.', '', $validate);
                }
                $this->addResponse($validate, 3, ['allowed_methods' => $this->core->core['settings']['security']['twofaSettings']['twofaUsing']]);
            } else {
                $this->addResponse($validate, 1);
            }

            return false;
        }

        if (PHP_SAPI === 'cli') {
            if (!$this->checkAccount($data)) {
                return false;
            }

            return true;
        }

        if (!$this->checkAccount($data)) {
            $this->access->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $this->access->ipFilter->removeFromMonitoring();

        $security = $this->getAccountSecurityObject();

        if (isset($this->app['enforce_2fa']) && $this->app['enforce_2fa'] == '1') {
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

        $this->setSessionAndRecaller($data);

        if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
            $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
        } else {
            $this->packagesData->redirectUrl = $this->links->url('home');
        }

        $this->logger->log->debug($this->account['email'] . ' authenticated successfully on app ' . $this->app['name']);

        return true;
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

    public function checkAccount(array $data, $viaProfile = null)
    {
        $this->account = $this->basepackages->accounts->checkAccount($data['user'], true);

        if ($this->account) {
            if ($this->account['status'] != '1') {
                $this->addResponse('Error: Username/Password incorrect!', 1);

                $this->logger->log->debug($data['user'] . ' is disabled!');

                return false;
            }

            //New App OR New account via rego
            $canLogin = $this->basepackages->accounts->canLogin($this->account['id'], $this->app['id']);

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
                if ($this->account['security']['forgotten_request'] == true) {
                    if (time() > $this->account['security']['forgotten_request_sent_on'] + ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForgottenPasswordTimeout'] ?? 60)
                    ) {
                        $this->account['security']['forgotten_request'] = null;
                        $this->account['security']['forgotten_request_session_id'] = null;
                        $this->account['security']['forgotten_request_ip'] = null;
                        $this->account['security']['forgotten_request_agent'] = null;
                        $this->account['security']['forgotten_request_code'] = null;
                        $this->account['security']['forgotten_request_sent_on'] = null;
                        $this->basepackages->accounts->addUpdateSecurity($this->account['id'], $this->account['security']);
                        $this->addResponse('Code Expired! Request new code...', 1);

                        return false;
                    }

                    if ($this->account['security']['forgotten_request_session_id'] !== $this->session->getId() ||
                        $this->account['security']['forgotten_request_ip'] !== $this->request->getClientAddress() ||
                        $this->account['security']['forgotten_request_agent'] !== $this->request->getUserAgent()
                    ) {
                        $this->addResponse('Error: OTP entered on a different browser than requested!', 1);

                        return false;
                    }

                    if ($this->secTools->checkPassword($data['pass'], $this->account['security']['forgotten_request_code'])) {//forgotten success and we remove forgotten fields
                        $this->account['security']['forgotten_request'] = null;
                        $this->account['security']['forgotten_request_session_id'] = null;
                        $this->account['security']['forgotten_request_ip'] = null;
                        $this->account['security']['forgotten_request_agent'] = null;
                        $this->account['security']['forgotten_request_code'] = null;
                        $this->account['security']['forgotten_request_sent_on'] = null;
                        $this->basepackages->accounts->addUpdateSecurity($this->account['id'], $this->account['security']);

                        return true;
                    }
                }

                if ($viaProfile) {
                    $this->addResponse('Error: Current Password incorrect!', 1);
                } else {
                    $this->addResponse('Error: Username/Password incorrect!', 1);
                }

                $this->logger->log->debug('Incorrect username/password entered by account ' . $this->account['email'] . ' on app ' . $this->app['name']);

                return false;
            }

            if ($this->account['security']['forgotten_request'] == true) {//We remove this as the user now remembers their password and logs in with it.
                $this->account['security']['forgotten_request'] = null;
                $this->account['security']['forgotten_request_session_id'] = null;
                $this->account['security']['forgotten_request_ip'] = null;
                $this->account['security']['forgotten_request_agent'] = null;
                $this->account['security']['forgotten_request_code'] = null;
                $this->basepackages->accounts->addUpdateSecurity($this->account['id'], $this->account['security']);
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the account exists.

            $this->addResponse('Error: Username/Password incorrect!', 1);

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
            $this->domains->getDomain()['name'],
            true
        );

        $this->cookies->send();

        $this->cookies->useEncryption(true);
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

        $this->account = $this->basepackages->accounts->getAccountById($hasIdentifier['account_id']);

        if ($this->account) {
            $this->updateSessionIdForSessionAndIdentifier($hasIdentifier);

            return true;
        }

        return false;
    }

    public function getoldSessionId()
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
            $this->account = $this->basepackages->accounts->getAccountById($this->session->get($this->getKey()));

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

    public function validateData(array $data, $task)
    {
        if ($task === 'auth') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid user name."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
            if (isset($this->app['enforce_2fa']) && $this->app['enforce_2fa'] == '1') {
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
        $accountsObj = $this->basepackages->accounts->getFirst('id', $this->account()['id']);

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
}