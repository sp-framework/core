<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Security as PhalconSecurity;

class SecTools
{
    public $security;

    public $random;

    public $crypt;

    public function __construct($security, $random, $crypt)
    {
        $this->security = $security;

        $this->random = $random;

        $this->crypt = $crypt;
    }

    public function init()
    {
        return $this;
    }

    public function hashPassword(string $password, $workFactor = null)
    {
        if ($workFactor) {
            $this->security->setWorkFactor($workFactor);
        }

        try {
            return $this->security->hash($password, $this->security->getWorkFactor());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checkPassword(string $password, string $hashedPassword)
    {
        return $this->security->checkHash($password, $hashedPassword);
    }

    public function passwordNeedsRehash(string $hashedPassword)
    {
        return password_needs_rehash(
            $hashedPassword,
            PASSWORD_BCRYPT,
            [
                'cost' => $this->security->getWorkFactor()
            ]
        );
    }
}