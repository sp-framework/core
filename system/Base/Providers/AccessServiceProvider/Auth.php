<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;

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
        $cookieKey = 'remember_' . $this->getKey();

        if ($this->user) {
            $this->clearUserRememberToken($cookieKey);
        }

        if ($this->cookies->has($cookieKey)) {
            $this->cookies->delete($cookieKey);
        }

        if ($this->session->has($this->key)) {
            $this->session->remove($this->key);
        }

        if ($this->session->has('_PHCOOKIE_' . $cookieKey)) {
            $this->session->remove('_PHCOOKIE_' . $cookieKey);
        }

        return true;
    }

    protected function clearUserRememberToken($cookieKey)
    {
        $user = $this->user;

        $user['remember_identifier'] = Json::decode($user['remember_identifier'], true);
        unset($user['remember_identifier'][$cookieKey]);
        $user['remember_identifier'] = Json::encode($user['remember_identifier']);

        $user['remember_token'] = Json::decode($user['remember_token'], true);
        unset($user['remember_token'][$cookieKey]);
        $user['remember_token'] = Json::encode($user['remember_token']);

        $this->users->update($user);
    }

    public function attempt($postData, $remember = false)
    {
        $this->user = $this->users->checkUserByEmail($postData['user']);

        if ($this->user) {

            $permissions = Json::decode($this->user['permissions'], true);

            if (!$permissions[strtolower($this->application['name'])]) {//Not allowed for application
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
        $cookieKey = 'remember_' . $this->getKey();

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

            throw new \Exception('Cannot set user from cookie');
        }
    }

    public function hasRecaller()
    {
        return $this->cookies->has('remember' . $this->getKey());
    }

    protected function setRememberToken()
    {
        $cookieKey = 'remember_' . $this->getKey();
        list($identifier, $token) = $this->recallerGenerate();

        $this->cookies->set(
            $cookieKey,
            $identifier . $this->separator . $token,
            time() + 86400
        );

        $this->cookies->get($cookieKey)->setOptions(['samesite'=>'strict']);

        $this->cookies->send();

        $this->user['remember_identifier'] = Json::encode([$cookieKey => $identifier]);

        $this->user['remember_token'] = Json::encode([$cookieKey => $this->secTools->hashPassword($token)]);

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
        $this->key = strtolower($this->application['name']);
    }

    public function setUserFromSession()
    {
        $this->user = $this->users->getById($this->session->get($this->getKey()));

        if (!$this->user) {
            throw new \Exception('User not found in session');
        }
    }

    protected function setUserSession()
    {
        $this->session->set($this->getKey(), $this->user['id']);
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