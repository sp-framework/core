<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Ip;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Exceptions\IpBlacklistedException;
use System\Base\Providers\AppsServiceProvider\Model\AppsIpBlackList;

class IpBlackList extends BasePackage
{
    public $clientAddress;

    public function setClientAddress($clientAddress = null)
    {
        if (!$clientAddress) {
            $this->clientAddress = $this->request->getClientAddress();
        } else {
            $this->clientAddress = $clientAddress;
        }
    }

    public function getClientAddress($clientAddress = null)
    {
        $this->setClientAddress($clientAddress);

        return $this->clientAddress;
    }

    public function checkList($clientAddress = null)
    {
        $this->getClientAddress($clientAddress);

        if ($this->clientAddress === "127.0.0.1") {
            return true;
        }

        if (!$this->apps->app['ip_black_list']) {//If null
            return true;
        }

        if (is_string($this->apps->app['ip_black_list'])) {
            $this->apps->app['ip_black_list'] = Json::decode($this->apps->app['ip_black_list'], true);
        }

        if (in_array($this->clientAddress, $this->apps->app['ip_black_list'])) {
            return false;
        }

        return true;
    }

    public function validateIp($ip)
    {
        $this->validation->add("ip_address", Ip::class,
            [
                "message"           => "Incorrect ip address.",
                "version"           => Ip::VERSION_4 | Ip::VERSION_6,
                "allowReserved"     => false,
                "allowPrivate"      => true,
                "allowEmpty"        => false
            ]
        );

        $validated = $this->validation->validate(["ip_address" => $ip])->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'];
            }

            return $messages;
        } else {
            return true;
        }
    }

    public function bumpIpBlacklistCounter()
    {
        if ($this->apps->getAppInfo()['incorrect_login_attempt_blacklist'] &&
            (int) $this->apps->getAppInfo()['incorrect_login_attempt_blacklist'] === 0
        ) {
            return;
        }

        $this->getClientAddress();

        $app = $this->apps->getFirst('id', $this->apps->getAppInfo()['id']);

        if ($app->getBlacklist()->count() > 0) {
            $appIncorrectLoginLimit = (int) $this->apps->getAppInfo()['incorrect_login_attempt_blacklist'];

            $blacklistEntry = $app->getBlacklist()->toArray()[0];

            $incorrectAttemptsCount = (int) $blacklistEntry['incorrect_attempts'];
            if ($incorrectAttemptsCount >= $appIncorrectLoginLimit) {
                $appInfo = $this->apps->processBlacklist($this->apps->getAppInfo(), $this->clientAddress);

                $this->apps->update($appInfo);

                $app->getBlacklist()->delete();
            } else {
                $newCount = $blacklistEntry['incorrect_attempts'] + 1;

                $app->getBlacklist()->update(['incorrect_attempts' => $newCount]);
            }

        } elseif ($app->getBlacklist()->count() === 0) {
            $newBlacklist = new AppsIpBlackList;

            $newBlacklist->assign([
                'app_id'                => $this->apps->getAppInfo()['id'],
                'ip_address'            => $this->clientAddress,
                'incorrect_attempts'    => 1
            ]);

            $newBlacklist->create();
        }
    }
}