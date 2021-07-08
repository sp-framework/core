<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Security as PhalconSecurity;

class SecTools
{
    protected $core;

    public $security;

    public $random;

    public $crypt;

    public function __construct($core, $security, $random, $crypt)
    {
        $this->core = $core;

        $this->security = $security;

        $this->random = $random;

        $this->crypt = $crypt;
    }

    public function init()
    {
        if ($this->crypt->getKey() === '') {
            $this->crypt->setKey($this->getSigKey());
        }

        return $this;
    }

    public function hashPassword(string $password, int $workFactor = null, int $defaultHash = 0)
    {
        if ($workFactor) {
            $this->security->setWorkFactor($workFactor);
        } else {
            $this->security->setWorkFactor($this->getSecWorkFactor());
        }

        if ($defaultHash) {
            $this->security->setDefaultHash($defaultHash);
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

    public function getSigKey()
    {
        if (!is_array($this->core->core['settings'])) {
            $this->core->core['settings'] = Json::decode($this->core->core['settings'], true);
        }

        return $this->core->core['settings']['sigKey'];
    }

    public function getCookiesSig()
    {
        if (!is_array($this->core->core['settings'])) {
            $this->core->core['settings'] = Json::decode($this->core->core['settings'], true);
        }

        return $this->core->core['settings']['cookiesSig'];
    }

    public function getSecWorkFactor()
    {
        if (!is_array($this->core->core['settings'])) {
            $this->core->core['settings'] = Json::decode($this->core->core['settings'], true);
        }

        return $this->core->core['settings']['security']['passwordWorkFactor'];
    }
}