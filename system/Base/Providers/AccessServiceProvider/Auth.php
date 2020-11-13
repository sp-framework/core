<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;

class Auth
{
    protected $key = 'id';

    protected $separator = '|';

    protected $session;

    protected $cookies;

    protected $users;

    protected $user;

    protected $application;

    protected $secTools;

    public function __construct($session, $cookies, $users, $applications, $secTools)
    {
        $this->session = $session;

        $this->cookies = $cookies;

        $this->users = $users;

        $this->application = $applications->getApplicationInfo();

        $this->secTools = $secTools;
    }

    public function init()
    {
        return $this;
    }

    public function logout()
    {
        $this->users->clearUserRememberToken($this->user['id']);

        $this->cookies->delete('remember');

        $this->session->remove($this->key);
        $this->session->remove('_PHCOOKIE_remember');

        return true;
    }

    public function attempt($postData, $remember = false)
    {
        $this->user = $this->users->checkUserByEmail($postData['user']);

        if ($this->user) {

            $canLogins = Json::decode($this->user['can_login'], true);

            if (!$canLogins[$this->application['id']]) {//Not allowed for application
                return false;
            }

            if (!$this->secTools->checkPassword($postData['pass'], $this->user['password'])) {//Password Fail
                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the user exists.

            return false;
        }

        if ($this->secTools->passwordNeedsRehash($this->user['password'])) {
            $this->user['password'] = $this->secTools->hashPassword($postData['pass']);

            $this->users->update($this->user);
        }

        $this->setUserSession();

        if ($postData['remember'] === 'true') {
            $this->setRememberToken();
        }

        return true;
    }

    public function setUserFromCookie()
    {
        list($identifier, $token) =
            explode($this->separator, $this->cookies->get('remember')->getValue());

        $identifierUser = $this->users->checkUserByIdentifier($identifier);
        if ($this->user !== $identifierUser) {

            $this->cookies->delete('remember');

            return;
        }

        if (!$this->secTools->checkPassword($token, $this->user['remember_token'])) {

            $this->users->clearUserRememberToken($this->user['id']);

            $this->cookies->delete('remember');

            throw new \Exception('Cannot set user from cookie');
        }
    }

    public function hasRecaller()
    {
        return $this->cookies->has('remember');
    }

    protected function setRememberToken()
    {
        list($identifier, $token) = $this->recallerGenerate();

        $this->cookies->set(
            'remember',
            $identifier . $this->separator . $token,
            time() + 86400
        );

        $this->cookies->get('remember')->setOptions(['samesite'=>'strict']);

        $this->cookies->send();

        $this->user['remember_identifier'] = $identifier;

        $this->user['remember_token'] = $this->secTools->hashPassword($token);

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
        return $this->session->has($this->key);
    }

    public function setUserFromSession()
    {
        $this->user = $this->users->getById($this->session->get($this->key));

        if (!$this->user) {
            throw new \Exception('User not found in session');
        }
    }

    protected function setUserSession()
    {
        $this->session->set($this->key, $this->user['id']);
    }

    public function generateNewPassword($complexity = 1, $length = 6)
    {
        // return substr(str_shuffle($this->setPasswordComplexityChars($complexity)), 0, $length);
    }

    public function checkAdminUser()
    {
        if ($this->userProvider->getByCompanyId()) {
            return false;
        }

        return true;
    }
}