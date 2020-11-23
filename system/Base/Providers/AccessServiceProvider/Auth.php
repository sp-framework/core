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

    protected $session;

    protected $cookies;

    protected $users;

    protected $user;

    protected $application;

    protected $secTools;

    protected $validation;

    protected $logger;

    protected $links;

    public $packagesData;

    public function __construct($session, $cookies, $users, $applications, $secTools, $validation, $logger, $links)
    {
        $this->session = $session;

        $this->cookies = $cookies;

        $this->users = $users;

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
        if (!$this->user) {
            $this->setUserFromSession();
        }

        $cookieKey = 'remember_' . $this->user['id'] . '_' . $this->getKey();

        if ($this->user) {
            $this->clearUserRememberToken($cookieKey);
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

        $this->logger->log->debug($this->user['email'] . ' logged out successfully from application: ' . $this->application['name']);

        return true;
    }

    protected function clearUserRememberToken($cookieKey)
    {
        if ($this->user['session_id']) {
            $this->user['session_id'] = null;
        }

        if ($this->user['remember_identifier']) {
            $this->user['remember_identifier'] = Json::decode($this->user['remember_identifier'], true);
            unset($this->user['remember_identifier'][$cookieKey]);
            $this->user['remember_identifier'] = Json::encode($this->user['remember_identifier']);
        }

        if ($this->user['remember_token']) {
            $this->user['remember_token'] = Json::decode($this->user['remember_token'], true);
            unset($this->user['remember_token'][$cookieKey]);
            $this->user['remember_token'] = Json::encode($this->user['remember_token']);
        }

        $this->users->update($this->user);

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

        if (!$this->checkUser($data)) {
            return false;
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

        if ($this->user['force_pwreset'] && $this->user['force_pwreset'] === '1') {

            $this->packagesData->redirectUrl = $this->links->url('auth/q/pwreset/true');

            return true;
        }

        $this->packagesData->redirectUrl = $this->links->url('/');

        if ($this->secTools->passwordNeedsRehash($this->user['password'])) {

            $this->user['password'] = $this->secTools->hashPassword($data['pass']);
            $this->user['can_login'] = Json::encode($this->user['can_login']);

            $this->users->update($this->user);
        }

        $this->setSessionAndToken($data);

        $this->logger->log->debug($this->user['email'] . ' authenticated successfully on application ' . $this->application['name']);

        return true;
    }

    protected function checkUser(array $data)
    {
        $this->user = $this->users->checkUserByEmail($data['user']);

        if ($this->user) {
            if ($this->user['can_login']) {
                $this->user['can_login'] = Json::decode($this->user['can_login'], true);

                if (!isset($this->user['can_login'][$this->application['route']])) {//New Application OR New user via rego

                    if ($this->application['can_login_role_ids']) {

                        $this->application['can_login_role_ids'] = Json::decode($this->application['can_login_role_ids'], true);

                        if (in_array($this->user['role_id'], $this->application['can_login_role_ids'])) {

                            $this->user['can_login'][$this->application['route']] = true;

                            $this->user['can_login'] = Json::encode($this->user['can_login']);

                            $this->users->update($this->user);

                        } else {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                            $this->logger->log->debug($this->user['email'] . ' and their role is not allowed to login to application ' . $this->application['name']);

                            return false;
                        }
                    }
                } else if (isset($this->user['can_login'][$this->application['route']]) && !$this->user['can_login'][$this->application['route']]) {//Not allowed for application
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                    $this->logger->log->debug($this->user['email'] . ' is not allowed to login to application ' . $this->application['name']);

                    return false;
                }
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                $this->logger->log->debug($this->user['email'] . ' is not allowed to login to application ' . $this->application['name']);

                return false;
            }

            if (!$this->secTools->checkPassword($data['pass'], $this->user['password'])) {//Password Fail
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

                $this->logger->log->debug('Incorrect username/password entered by user ' . $this->user['email'] . ' on application ' . $this->application['name']);

                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the user exists.

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

            $this->logger->log->debug($data['user'] . ' is not in DB. Application: ' . $this->application['name']);

            return false;
        }

        return true;
    }

    protected function setSessionAndToken(array $data)
    {
        if ($this->setUserSession()) {
            $this->user['session_id'] = $this->session->getId();

            if (is_array($this->user['can_login'])) {
                $this->user['can_login'] = Json::encode($this->user['can_login']);
            }

            $this->users->update($this->user);
        }

        if (isset($data['remember']) && $data['remember'] === 'true') {
            $this->setRememberToken();
        }
    }

    public function setUserFromCookie()
    {
        $cookieKey = 'remember_' . $this->user['id'] . '_' . $this->getKey();

        $userToken = Json::decode($this->user['remember_token'], true)[$cookieKey];

        list($identifier, $token) =
            explode($this->separator, $this->cookies->get($cookieKey)->getValue());

        $identifierUser = $this->users->checkUserByIdentifier($identifier);

        if ($this->user !== $identifierUser) {

            $this->cookies->delete($cookieKey);

            return;
        }

        if (!$this->secTools->checkPassword($token, $userToken)) {

            $this->clearUserRememberToken($cookieKey);

            $this->cookies->delete($cookieKey);

            $this->logger->log->debug('Cannot set user : ' . $this->user['email'] . ' via cookie for application: ' . $this->application['name']);

            throw new \Exception('Cannot set user from cookie');
        }
    }

    public function hasRecaller()
    {
        return $this->cookies->has('remember' . $this->getKey());
    }

    protected function setRememberToken()
    {
        $cookieKey = 'remember_' . $this->user['id'] . '_' . $this->getKey();

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

        if ($this->user['remember_identifier'] && $this->user['remember_identifier'] !== '') {
            $this->user['remember_identifier'] = Json::decode($this->user['remember_identifier'], true);
        } else {
            $this->user['remember_identifier'] = [];
        }
        $this->user['remember_identifier'][$cookieKey] = $identifier;
        $this->user['remember_identifier'] = Json::encode($this->user['remember_identifier']);

        if ($this->user['remember_token'] && $this->user['remember_token'] !== '') {
            $this->user['remember_token'] = Json::decode($this->user['remember_token'], true);
        } else {
            $this->user['remember_token'] = [];
        }
        $this->user['remember_token'][$cookieKey] = $this->secTools->hashPassword($token);
        $this->user['remember_token'] = Json::encode($this->user['remember_token']);

        $this->users->update($this->user);
    }

    protected function recallerGenerate()
    {
        return [bin2hex($this->secTools->random->bytes()), bin2hex($this->secTools->random->bytes())];
    }

    public function user()
    {
        return $this->user;
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
            $this->user = $this->users->getById($this->session->get($this->getKey()));
        } else {
            return false;
        }

        if (!$this->user['session_id']) {
            $this->logger->log->debug($this->user['email'] . ' session null, perhaps was forced logged out by Administrator.');

            throw new \Exception('User session deleted in DB by administrator via force logout.');
        }

        if (!$this->user) {
            $this->logger->log->debug($this->user['email'] . ' not found in session for application: ' . $this->application['name']);

            throw new \Exception('User not found in session');
        }
    }

    protected function setUserSession()
    {
        $this->session->set($this->getKey(), $this->user['id']);

        return true;
    }

    protected function validateData(array $data, $task)
    {
        if ($task === 'auth') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid username."]);
            $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);
        } else if ($task === 'forgot') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid username."]);
        } else if ($task === 'reset') {
            $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid username."]);
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

        $this->user = $this->users->checkUserByEmail($data['user']);

        if ($this->user) {
            $this->user['force_logout'] = '1';

            $this->user['email_new_password'] = '1';

            $this->users->updateUser($this->user);

            $this->logger->log->info('New password requested for user ' . $this->user['email'] . ' via forgot password. New password was emailed to the user.');
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

        if (!$this->checkUser($data)) {
            return false;
        }

        $this->user['password'] = $this->secTools->hashPassword($data['newpass']);
        $this->user['force_pwreset'] = null;
        $this->setSessionAndToken($data);
        $this->users->updateUser($this->user);

        $this->logger->log->info('Password reset successfull for user ' . $this->user['email'] . ' via pwreset.');

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Password changed. Redirecting...';

        $this->packagesData->redirectUrl = $this->links->url('/');

        return true;
    }
}