<?php

namespace System\Base\Providers\AccessServiceProvider;

use Apps\Dash\Packages\System\Tools\Qrcodes\Qrcodes;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;
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

    protected $sessionTools;

    protected $cookies;

    protected $cookieKey;

    protected $accounts;

    protected $account = null;

    protected $app;

    protected $secTools;

    protected $validation;

    protected $logger;

    protected $links;

    protected $profile;

    protected $email;

    protected $emailQueue;

    protected $domains;

    public $packagesData;

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
        $email,
        $emailQueue,
        $domains
    ) {
        $this->request = $request;

        $this->config = $config;

        $this->session = $session;

        $this->sessionTools = $sessionTools;

        $this->cookies = $cookies;

        $this->app = $apps->getAppInfo();

        $this->secTools = $secTools;

        $this->validation = $validation;

        $this->logger = $logger;

        $this->links = $links;

        $this->accounts = $accounts;

        $this->profile = $profile;

        $this->email = $email;

        $this->emailQueue = $emailQueue;

        $this->domains = $domains;

        $this->packagesData = new PackagesData;
    }

    public function init()
    {
        $this->cookieKey = 'remember_' . $this->getKey();

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

        $this->packagesData->redirectUrl = $this->links->url('auth');

        $this->logger->log->debug($this->account['email'] . ' logged out successfully from app: ' . $this->app['name']);

        return true;
    }

    protected function clearAccountRecaller()
    {
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
            return false;
        }

        if ($this->account['two_fa_status'] == '1' &&
            !isset($data['code'])
        ) {
            $this->packagesData->responseCode = 3;

            $this->packagesData->responseMessage = '2FA Code Required';

            return false;
        }

        if (($this->account['two_fa_status'] == '1' && isset($data['code'])) &&
            !$this->verifyTwoFa($data['code'])
        ) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password/2FA Code incorrect!';

            return false;
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

        if ($this->account['force_pwreset'] && $this->account['force_pwreset'] === '1') {

            $this->packagesData->redirectUrl = $this->links->url('auth/q/pwreset/true');

            return true;
        }

        if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
            $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
        } else {
            $this->packagesData->redirectUrl = $this->links->url('home');
        }

        if ($this->secTools->passwordNeedsRehash($this->account['password'])) {
            $this->account['password'] = $this->secTools->hashPassword($data['pass'], $this->config->security->passwordWorkFactor);
        }

        $this->setSessionAndRecaller($data);

        $this->logger->log->debug($this->account['email'] . ' authenticated successfully on app ' . $this->app['name']);

        return true;
    }

    protected function checkAccount(array $data, $viaProfile = null)
    {
        $this->account = $this->accounts->checkAccountByEmail($data['user'], true);

        if ($this->account) {
            //New App OR New account via rego
            if (!$this->accounts->canLogin($this->account['id'], $this->app['route'])) {

                if ($this->app['can_login_role_ids']) {

                    $this->app['can_login_role_ids'] = Json::decode($this->app['can_login_role_ids'], true);

                    if (in_array($this->account['role_id'], $this->app['can_login_role_ids'])) {

                        $canloginModel = new BasepackagesUsersAccountsCanlogin;

                        $newLogin['account_id'] = $this->account['id'];
                        $newLogin['app'] = $this->app['route'];
                        $newLogin['allowed'] = '1';

                        $canloginModel->assign($newLogin);

                        $canloginModel->create();
                    } else {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                        $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                        return false;
                    }
                }
            }

            if (!$this->secTools->checkPassword($data['pass'], $this->account['password'])) {//Password Fail
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
            $sessionModel = new BasepackagesUsersAccountsSessions;

            $newSession['account_id'] = $this->account['id'];
            $newSession['app'] = $this->getKey();
            $newSession['session_id'] = $this->session->getId();

            $sessionModel->assign($newSession);

            try {
                $sessionModel->create();
            } catch (\Exception $e) {

                $this->logger->log->debug('Duplicate session Id Found. This happens when session was deleted from server and browser used an old session ID.');

                $this->logout();

                throw $e;
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
            time() + 86400,
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

        $this->account = $this->accounts->getAccountById($hasIdentifier['account_id'], true);

        if ($this->account) {
            $this->updateSessionIdForSessionAndIdentifier($hasIdentifier);

            $this->setAccountProfile();

            return true;
        }

        return false;
    }

    //Old session expired in browser, update session ids in db, else we will get stale entry in db during logout.
    protected function updateSessionIdForSessionAndIdentifier($identifier)
    {
        $oldSessionId = $identifier['session_id'];

        $identifierModel = new BasepackagesUsersAccountsIdentifiers;

        $identifier['session_id'] = $this->session->getId();
        $identifierModel->assign($identifier);
        $identifierModel->update();

        $sessionModel = new BasepackagesUsersAccountsSessions;

        $session = $sessionModel::findFirst(
            [
            'session_id = :sessionId:',
            'bind'      => ['sessionId' => $oldSessionId]
            ]
        );

        if ($session) {
            $session = $session->toArray();
            $session['session_id'] = $this->session->getId();
            $sessionModel->assign($session);
            $sessionModel->update();
        }

        $this->setUserSession();
    }

    public function hasRecaller()
    {
        return $this->cookies->has($this->cookieKey);
    }

    protected function setRecaller()
    {
        list($identifier, $token) = $this->generateRecaller();

        $this->cookies->set(
            $this->cookieKey,
            $identifier . $this->separator . $token,
            time() + 86400,
            '/',
            null,
            null,
            true
        );

        $this->cookies->get($this->cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->send();

        //Add to db
        $identifierModel = new BasepackagesUsersAccountsIdentifiers;

        $newIdentifier['account_id'] = $this->account['id'];
        $newIdentifier['app'] = $this->getKey();
        $newIdentifier['session_id'] = $this->session->getId();
        $newIdentifier['identifier'] = $identifier;
        $newIdentifier['token'] = $this->secTools->hashPassword($token, $this->config->security->cookiesWorkFactor);

        $identifierModel->assign($newIdentifier);

        $identifierModel->create();
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
            $this->account = $this->accounts->getAccountById($this->session->get($this->getKey()), true);

            if (!$this->account) {
                $this->logger->log->debug($this->account['email'] . ' not found in session for app: ' . $this->app['name']);

                throw new \Exception('User not found in session');
            }

            if (!$this->accounts->hasSession($this->account['id'], $this->session->getId())) {
                $this->logger->log->debug($this->account['email'] . ' session id ' . $this->session->getId() . ' not present in DB.');

                $this->sessionTools->clearSession($this->session->getId());

                throw new \Exception('User session deleted in DB by administrator via force logout.');
            }

            $this->setAccountProfile();
        } else {
            return false;
        }
    }

    protected function setAccountProfile()
    {
        $this->account['profile'] = $this->profile->profile($this->account['id']);
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

        $this->account = $this->accounts->checkAccountByEmail($data['user'], true);

        if ($this->account) {
            $this->account['force_logout'] = '1';

            $this->account['email_new_password'] = '1';

            $this->accounts->updateAccount($this->account);

            $this->logger->log->info('New password requested for account ' . $this->account['email'] . ' via forgot password. New password was emailed to the account.');
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

        $this->account['password'] = $this->secTools->hashPassword($data['newpass'], $this->config->security->passwordWorkFactor);
        $this->account['force_pwreset'] = null;
        // $this->setSessionAndRecaller($data);
        $this->accounts->updateAccount($this->account);

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
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new \ZxcvbnPhp\Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseData = $result['score'];

            $this->packagesData->responseMessage = 'Checking Password Strength Success';

            return true;
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Error Checking Password Strength';
    }

    public function generateNewPassword()
    {
        $this->packagesData->responseCode = 0;

        $this->packagesData->responseData = $this->secTools->random->base62(12);;

        $this->packagesData->responseMessage = 'Password Generate Successfully';
    }

    public function enableTwoFa()
    {
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

            return true;
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            return false;
        }
    }

    public function enableVerifyTwoFa(int $code)
    {
        if ($this->account['two_fa_status'] &&
            $this->account['two_fa_status'] == '1'
        ) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA already enabled! Contact Administrator.";

            return false;
        }

        if ($this->verifyTwoFa($code)) {
            unset($this->account['profile']);

            $this->account['two_fa_status'] = '1';

            $this->accounts->update($this->account);
        }
    }

    public function disableTwoFa(int $code)
    {
        $totp = TOTP::create($this->account['two_fa_secret']);

        if ($totp->verify($code)) {
            $this->account['two_fa_status'] = null;

            $this->account['two_fa_secret'] = null;

            $this->accounts->update($this->account);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = "2FA disabled.";
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "2FA disable failed.";
        }
    }

    public function verifyTwoFa(int $code)
    {
        $totp = TOTP::create($this->account['two_fa_secret']);

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
        unset($this->account['profile']);

        $this->account['two_fa_secret'] = trim(Base32::encodeUpper(random_bytes(16)), '=');

        $this->accounts->update($this->account);

        return $this->account['two_fa_secret'];
    }

    public function checkAgent()
    {
        $clientAddress = $this->request->getClientAddress();
        $userAgent = $this->request->getUserAgent();
        $sessionId = $this->session->getId();

        $accountsObj = $this->accounts->getModelToUse()::findFirstById($this->account()['id']);

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

                if ($agent['client_address'] === $clientAddress &&
                    $agent['user_agent'] === $userAgent &&
                    $agent['session_id'] === $sessionId &&
                    $agent['account_id'] === $this->account()['id'] &&
                    $agent['verified'] == '1'
                ) {
                    return true;
                } else if ($agent['session_id'] === $sessionId &&
                           $agent['verified'] == '1'
                ) {
                    $this->logger->log->emergency('Same session being used by another browser! Probably session hijack!');

                    $this->account['force_logout'] = '1';

                    $this->accounts->update($this->account);

                    $this->logout();

                    return false;
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
                }
            } else {
                if (!$this->email->setup()) {
                    $verified = 1;
                } else {
                    $verified = 0;
                }

                $newAgent =
                    [
                        'account_id'        => $this->account['id'],
                        'session_id'        => $sessionId,
                        'client_address'    => $clientAddress,
                        'user_agent'        => $userAgent,
                        'verified'          => $verified
                    ];

                $agentsObj = new BasepackagesUsersAccountsAgents;

                $agentsObj->assign($newAgent);

                try {
                    $agentsObj->create();
                } catch (\Exception $e) {
                    $this->logout();

                    throw $e;
                }
            }
        } else {
            if (!$this->email->setup()) {
                $verified = 1;
            } else {
                $verified = 0;
            }

            $newAgent =
                [
                    'account_id'        => $this->account['id'],
                    'session_id'        => $sessionId,
                    'client_address'    => $clientAddress,
                    'user_agent'        => $userAgent,
                    'verified'          => $verified
                ];

            $agentsObj = new BasepackagesUsersAccountsAgents;

            $agentsObj->assign($newAgent);

            try {
                $agentsObj->create();
            } catch (\Exception $e) {
                $this->logout();

                throw $e;
            }
        }

        //If Email is not configured, we cannot send new passcodes.
        if (!$this->email->setup()) {
            return true;
        }

        return false;
    }

    public function sendVerificationEmail()
    {
        if (!$this->account) {
            $this->setUserFromSession();
        }

        $accountsObj = $this->accounts->getModelToUse()::findFirstById($this->account()['id']);

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

            $code = $this->secTools->random->base62(12);

            $agentObj->assign(['verification_code' => $code])->update();

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

            $this->packagesData->responseMessage = 'Error Sending Email! Please contact administrator.';

            $this->packagesData->responseCode = 1;

            return;
        }

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
        $emailData['to_addresses'] = Json::encode([$this->account['email']]);
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

        $accountsObj = $this->accounts->getModelToUse()::findFirstById($this->account()['id']);

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
            $agent = $agentObj->toArray();

            if ($agent['verification_code'] === $data['code']) {
                if ($agent['client_address'] === $clientAddress &&
                    $agent['user_agent'] === $userAgent &&
                    $agent['session_id'] === $sessionId &&
                    $agent['account_id'] === $this->account()['id'] &&
                    $agent['verified'] == '0'
                ) {
                    $agentObj->assign(['verified' => '1', 'verification_code' => null])->update();

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

            return;
        }

        $this->packagesData->responseMessage = 'Please contact administrator.';

        $this->packagesData->responseCode = 1;
    }
}