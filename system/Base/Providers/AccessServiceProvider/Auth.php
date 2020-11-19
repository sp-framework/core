<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Helper\Json;
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

    public $packagesData;

    public function __construct($session, $cookies, $users, $applications, $secTools, $validation, $logger)
    {
        $this->session = $session;

        $this->cookies = $cookies;

        $this->users = $users;

        $this->application = $applications->getApplicationInfo();

        $this->secTools = $secTools;

        $this->validation = $validation;

        $this->logger = $logger;

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
        $this->user['remember_identifier'] = Json::decode($this->user['remember_identifier'], true);
        unset($this->user['remember_identifier'][$cookieKey]);
        $this->user['remember_identifier'] = Json::encode($this->user['remember_identifier']);

        $this->user['remember_token'] = Json::decode($this->user['remember_token'], true);
        unset($this->user['remember_token'][$cookieKey]);
        $this->user['remember_token'] = Json::encode($this->user['remember_token']);

        $this->users->update($this->user);

        //Set cookies to 1 second so browser removes them.
        $this->cookies->set(
            $cookieKey,
            '0',
            1
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

    public function attempt($postData, $remember = false)
    {
        $validate = $this->validateData($postData);

        if ($validate !== true) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

            return false;
        }

        $this->user = $this->users->checkUserByEmail($postData['user']);

        if ($this->user) {

            if ($this->user['can_login']) {
                $canLogin = Json::decode($this->user['can_login'], true);

                if (!isset($canLogin[$this->application['name']]) ||
                    (isset($canLogin[$this->application['name']]) && !$canLogin[$this->application['name']])
                ) {//Not allowed for application

                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                    $this->logger->log->debug('User is not allowed to login to application ' . $this->application['name']);

                    return false;
                }
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Contact System Administrator';

                $this->logger->log->debug('User is not allowed to login to application ' . $this->application['name']);

                return false;
            }

            if (!$this->secTools->checkPassword($postData['pass'], $this->user['password'])) {//Password Fail
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

                $this->logger->log->debug('Incorrect username/password entered by user ' . $this->user['email'] . ' on application ' . $this->application['name']);

                return false;
            }
        } else {
            $this->secTools->hashPassword(rand());//Randomize so we take same time to respond as if the user exists.

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error: Username/Password incorrect!';

            $this->logger->log->debug($this->user['email'] . ' is not in DB. Application: ' . $this->application['name']);

            return false;
        }

        if ($this->secTools->passwordNeedsRehash($this->user['password'])) {

            $this->user['password'] = $this->secTools->hashPassword($postData['pass']);

            $this->users->update($this->user);
        }

        if ($this->setUserSession()) {
            $this->user['session_id'] = $this->session->getId();

            $this->users->update($this->user);
        }

        if ($postData['remember'] === 'true') {
            $this->setRememberToken();
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Authenticated. Redirecting...';

        $this->logger->log->debug($this->user['email'] . ' authenticated successfully on application ' . $this->application['name']);

        return true;
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
            time() + 86400
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
        $this->key = strtolower($this->application['name']);
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

    public function loginPermit()
    {
        var_dump($this->user);
        return false;
    }
    // public function generateNewPassword($complexity = 1, $length = 6)
    // {
            // return substr(str_shuffle($this->setPasswordComplexityChars($complexity)), 0, $length);
    // }

    // public function checkAdminUser()
    // {
    //     if ($this->userProvider->getByCompanyId()) {
    //         return false;
    //     }

    //     return true;
    // }

    protected function validateData(array $data)
    {
        $this->validation->add('user', PresenceOf::class, ["message" => "Enter valid username."]);
        $this->validation->add('pass', PresenceOf::class, ["message" => "Enter valid password."]);

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
}