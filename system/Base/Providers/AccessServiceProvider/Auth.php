<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Auth
{
    protected $key = null;

    protected $separator = '|';

    protected $config;

    protected $session;

    protected $sessionTools;

    protected $cookies;

    protected $accounts;

    protected $account;

    protected $application;

    protected $secTools;

    protected $validation;

    protected $logger;

    protected $links;

    public $packagesData;

    public function __construct(
        $config,
        $session,
        $sessionTools,
        $cookies,
        $accounts,
        $applications,
        $secTools,
        $validation,
        $logger,
        $links
    ) {
        $this->config = $config;

        $this->session = $session;

        $this->sessionTools = $sessionTools;

        $this->cookies = $cookies;

        $this->accounts = $accounts;

        $this->application = $applications->getApplicationInfo();

        $this->secTools = $secTools;

        $this->validation = $validation;

        $this->logger = $logger;

        $this->links = $links;

        $this->packagesData = new PackagesData;
    }

    public function init()
    {
        return $this;
    }

    public function logout()
    {
        if (!$this->account) {
            $this->setUserFromSession();
        }

        $cookieKey = 'remember_' . $this->account['id'] . '_' . $this->getKey();

        if ($this->account) {
            $this->clearAccountSessionId();
            $this->clearAccountRememberToken($cookieKey);
        }

        if ($this->cookies->has($cookieKey)) {
            $this->cookies->delete($cookieKey);
        }

        if ($this->session->has('_PHCOOKIE_' . $cookieKey)) {
            $this->session->remove('_PHCOOKIE_' . $cookieKey);
        }

        if ($this->session->has($this->key)) {
            $this->session->remove($this->key);
        }

        $this->logger->log->debug($this->account['email'] . ' logged out successfully from application: ' . $this->application['name']);

        return true;
    }

    protected function clearAccountSessionId()
    {
        if ($this->account['session_ids']) {
            if (!is_array($this->account['session_ids'])) {
                $this->account['session_ids'] = Json::decode($this->account['session_ids'], true);
            }
        }
        $sessionIdKey = array_search($this->session->getId(), $this->account['session_ids']);

        if ($sessionIdKey !== false) {
            if (count($this->account['session_ids']) === 1) {
                $this->account['session_ids'] = null;
            } else {
                unset($this->account['session_ids'][$sessionIdKey]);

                $this->account['session_ids'] = Json::encode($this->account['session_ids']);
            }
        }

        $this->sessionTools->clearSession($this->session->getId());

        $this->accounts->update($this->account);
    }

    protected function clearAccountRememberToken($cookieKey)
    {
        if ($this->account['remember_identifier']) {
            $this->account['remember_identifier'] = Json::decode($this->account['remember_identifier'], true);
            unset($this->account['remember_identifier'][$cookieKey]);
            $this->account['remember_identifier'] = Json::encode($this->account['remember_identifier']);
        }

        if ($this->account['remember_token']) {
            $this->account['remember_token'] = Json::decode($this->account['remember_token'], true);
            unset($this->account['remember_token'][$cookieKey]);
            $this->account['remember_token'] = Json::encode($this->account['remember_token']);
        }

        $this->accounts->update($this->account);

        //Set cookies to 1 second so browser removes them.
        $this->cookies->set(
            $cookieKey,
            '0',
            1,
            '/',
            null,
            null,
            true
        );
        $this->cookies->get($cookieKey)->setOptions(['samesite'=>'strict']);
        $this->cookies->send();

        if ($this->cookies->has($cookieKey)) {
            $this->cookies->delete($cookieKey);
        }

        if ($this->session->has('_PHCOOKIE_' . $cookieKey)) {
            $this->session->remove('_PHCOOKIE_' . $cookieKey);
        }
    }

    public function attempt($data, $remember = false)
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

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

        if ($this->account['force_pwreset'] && $this->account['force_pwreset'] === '1') {

            $this->packagesData->redirectUrl = $this->links->url('auth/q/pwreset/true');

            return true;
        }

        $this->packagesData->redirectUrl = $this->links->url('/');

        if ($this->secTools->passwordNeedsRehash($this->account['password'])) {

            $this->account['password'] = $this->secTools->hashPassword($data['pass'], $this->config->security->passwordWorkFactor);
            if (is_array($this->account['can_login'])) {
                $this->account['can_login'] = Json::encode($this->account['can_login']);
            }

            $this->accounts->update($this->account);
        }

        $this->setSessionAndToken($data);

        $this->logger->log->debug($this->account['email'] . ' authenticated successfully on application ' . $this->application['name']);

        return true;
    }

    protected function checkAccount(array $data)
    {
        $this->account = $this->accounts->checkAccountByEmail($data['user']);

        if ($this->account) {
            if ($this->account['can_login']) {
                $this->account['can_login'] = Json::decode($this->account['can_login'], true);
            }

            if (isset($this->account['can_login'][$this->application['route']]) &&
                !$this->account['can_login'][$this->application['route']]
            ) {//Not allowed for application
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                $this->logger->log->debug($this->account['email'] . ' is not allowed to login to application ' . $this->application['name']);

                return false;
            }

            if (!isset($this->account['can_login'][$this->application['route']])) {//New Application OR New account via rego

                if ($this->application['can_login_role_ids']) {

                    $this->application['can_login_role_ids'] = Json::decode($this->application['can_login_role_ids'], true);

                    if (in_array($this->account['role_id'], $this->application['can_login_role_ids'])) {

                        $this->account['can_login'][$this->application['route']] = true;

                        $this->account['can_login'] = Json::encode($this->account['can_login']);

                        $this->accounts->update($this->account);

                    } else {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                        $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to application ' . $this->application['name']);

                        return false;
                    }
                }
            }

            if (!$this->secTools->checkPassword($data['pass'], $this->account['password'])) {//Password Fail
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

                $this->logger->log->debug('Incorrect username/password entered by account ' . $this->account['email'] . ' on application ' . $this->application['name']);

                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the account exists.

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

            $this->logger->log->debug($data['account'] . ' is not in DB. Application: ' . $this->application['name']);

            return false;
        }

        return true;
    }

    protected function setSessionAndToken(array $data)
    {
        if ($this->setUserSession()) {
            if ($this->account['session_ids']) {
                $this->account['session_ids'] = Json::decode($this->account['session_ids'], true);
                array_push($this->account['session_ids'], $this->session->getId());
            } else {
                $this->account['session_ids'] = [];
                array_push($this->account['session_ids'], $this->session->getId());
            }

            $this->account['session_ids'] = Json::encode($this->account['session_ids']);

            if (is_array($this->account['can_login'])) {
                $this->account['can_login'] = Json::encode($this->account['can_login']);
            }

            $this->accounts->update($this->account);
        }

        if (isset($data['remember']) && $data['remember'] === 'true') {
            $this->setRememberToken();
        }
    }

    public function setUserFromCookie()
    {
        $cookieKey = 'remember_' . $this->account['id'] . '_' . $this->getKey();

        if ($this->account['remember_token']) {
            $accountToken = Json::decode($this->account['remember_token'], true)[$cookieKey];
        } else {
            $accountToken = '';
        }

        if ($this->account['remember_identifier']) {
            $accountIdentifier = Json::decode($this->account['remember_identifier'], true)[$cookieKey];
        } else {
            $accountIdentifier = '';
        }

        list($identifier, $token) = explode($this->separator, $this->cookies->get($cookieKey)->getValue());

        if ($accountIdentifier !== $identifier) {

            $this->cookies->delete($cookieKey);

            return;
        }

        if (!$this->secTools->checkPassword($token, $accountToken)) {

            $this->clearAccountRememberToken($cookieKey);

            $this->cookies->delete($cookieKey);

            $this->logger->log->debug(
                'Cannot set account : ' . $this->account['email'] . ' via cookie for application: ' . $this->application['name']
            );

            throw new \Exception('Cannot set account from cookie');
        }

        return true;
    }

    public function hasRecaller()
    {
        if ($this->account) {
            return $this->cookies->has('remember_' . $this->account['id'] . '_' . $this->getKey());
        }

        return false;
    }

    protected function setRememberToken()
    {
        $cookieKey = 'remember_' . $this->account['id'] . '_' . $this->getKey();

        list($identifier, $token) = $this->recallerGenerate();

        $this->cookies->set(
            $cookieKey,
            $identifier . $this->separator . $token,
            time() + 86400,
            '/',
            null,
            null,
            true
        );

        $this->cookies->get($cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->send();

        if ($this->account['remember_identifier'] && $this->account['remember_identifier'] !== '') {
            $this->account['remember_identifier'] = Json::decode($this->account['remember_identifier'], true);
        } else {
            $this->account['remember_identifier'] = [];
        }
        $this->account['remember_identifier'][$cookieKey] = $identifier;
        $this->account['remember_identifier'] = Json::encode($this->account['remember_identifier']);

        if ($this->account['remember_token'] && $this->account['remember_token'] !== '') {
            $this->account['remember_token'] = Json::decode($this->account['remember_token'], true);
        } else {
            $this->account['remember_token'] = [];
        }

        $this->account['remember_token'][$cookieKey] = $this->secTools->hashPassword($token, $this->config->security->cookiesWorkFactor);
        $this->account['remember_token'] = Json::encode($this->account['remember_token']);

        $this->accounts->update($this->account);
    }

    protected function recallerGenerate()
    {
        return [bin2hex($this->secTools->random->bytes()), bin2hex($this->secTools->random->bytes())];
    }

    public function account()
    {
        return $this->account;
    }

    public function check()
    {
        return $this->hasUserInSession();
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
        $this->key = strtolower($this->application['route']);
    }

    public function setUserFromSession()
    {
        if ($this->session->get($this->getKey())) {
            $this->account = $this->accounts->getById($this->session->get($this->getKey()));
        } else {
            return false;
        }

        if (!$this->account['session_ids']) {
            $this->logger->log->debug($this->account['email'] . ' session null, perhaps was forced logged out by Administrator.');

            throw new \Exception('User session deleted in DB by administrator via force logout.');
        } else if ($this->account['session_ids']) {
            $this->account['session_ids'] = Json::decode($this->account['session_ids'], true);

            if (!in_array($this->session->getId(), $this->account['session_ids'])) {
                $this->logger->log->debug($this->account['email'] . ' session id ' . $this->session->getId() . ' not present in DB.');

                $this->sessionTools->clearSession($this->session->getId());

                throw new \Exception('User session deleted in DB by administrator via force logout.');
            }
        }

        if (!$this->account) {
            $this->logger->log->debug($this->account['email'] . ' not found in session for application: ' . $this->application['name']);

            throw new \Exception('User not found in session');
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

        $this->account = $this->accounts->checkAccountByEmail($data['user']);

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

    public function resetPassword(array $data)
    {
        $validate = $this->validateData($data, 'reset');

        if ($validate !== true) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

            return false;
        }

        if (!$this->checkAccount($data)) {
            return false;
        }

        $this->account['password'] = $this->secTools->hashPassword($data['newpass'], $this->config->security->passwordWorkFactor);
        $this->account['force_pwreset'] = null;
        $this->setSessionAndToken($data);
        $this->accounts->updateAccount($this->account);

        $this->logger->log->info('Password reset successful for account ' . $this->account['email'] . ' via pwreset.');

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Password changed. Redirecting...';

        $this->packagesData->redirectUrl = $this->links->url('/');

        return true;
    }
}