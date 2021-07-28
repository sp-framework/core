<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Apps\Dash\Packages\System\Messenger\Messenger;
use Phalcon\Helper\Json;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;

class Pusher extends BasePackage implements WampServerInterface
{
    protected $subscriptions = [];

    protected $accountsObj;

    protected $tunnelsObj;

    public function init()
    {
        $this->tunnelsObj = new BasepackagesUsersAccountsTunnels;

        return $this;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->subscriptions[$topic->getId()] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        //
    }

    public function onOpen(ConnectionInterface $conn)
    {
        var_dump('Open: '. $conn->resourceId);
        if (!$this->checkAccount($conn)) {
            $conn->close();
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        var_dump('Close: '.  $conn->resourceId);
        $this->markMessengerAway($conn->resourceId);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        var_dump($e);
        // Close on Error
        $conn->close();
    }

    public function onNewPush($newPush)
    {
        $newPush = Json::decode($newPush, true);

        if (!array_key_exists($newPush['type'], $this->subscriptions)) {
            return;
        }

        $topic = $this->subscriptions[$newPush['type']];

        if (!isset($newPush['broadcast']) ||
            (isset($newPush['broadcast']) && !$newPush['broadcast'])
        ) {
            $excludeUsers = [];
            $eligibleUsers = [];

            foreach ($topic->getIterator() as $key => $connection) {
                if ($connection->resourceId != $newPush['to']) {
                    array_push($excludeUsers, $connection->WAMP->sessionId);
                } else if ($connection->resourceId == $newPush['to']) {
                    array_push($eligibleUsers, $connection->WAMP->sessionId);
                }
            }

            $topic->broadcast($newPush['response'], $excludeUsers, $eligibleUsers);
        } else if (isset($newPush['broadcast']) && isset($newPush['from'])) {//If from set, it will not broadcast to from
            $excludeUsers = [];
            $eligibleUsers = [];

            foreach ($topic->getIterator() as $key => $connection) {
                if ($connection->resourceId != $newPush['from']) {
                    array_push($eligibleUsers, $connection->WAMP->sessionId);
                } else if ($connection->resourceId == $newPush['from']) {
                    array_push($excludeUsers, $connection->WAMP->sessionId);
                }
            }

            $topic->broadcast($newPush['response'], $excludeUsers);
        } else {
            $topic->broadcast($newPush['response']);
        }
    }

    protected function checkAccount($conn)
    {
        $cookiesArr = $conn->httpRequest->getHeaders()['Cookie'][0];

        $cookiesArr = explode(';', $cookiesArr);

        $cookies = [];

        foreach ($cookiesArr as $cookie) {
            $cookie = explode('=', $cookie);
            $cookies[trim($cookie[0])] = trim($cookie[1]);
        }

        $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($cookies['id']);

        $this->account = $this->accountsObj->toArray();

        if (!$this->account) {
            return false;
        }

        $this->account['profile'] = $this->basepackages->profile->getProfile($this->account['id']);

        if ($this->checkSession($cookies)) {
            if (!$this->accountsObj->tunnels) {
                $newTunnel =
                    [
                        'account_id'            => $this->account['id'],
                        'notifications_tunnel'  => $conn->resourceId
                    ];


                $this->tunnelsObj->assign($newTunnel);

                $this->tunnelsObj->create();
            } else {
                $this->accountsObj->tunnels->assign(['notifications_tunnel'  => $conn->resourceId])->update();
            }
            return true;
        }

        return false;
    }

    protected function checkSession($cookies)
    {
        if ($this->accountsObj->sessions) {
            foreach ($this->accountsObj->sessions as $key => $session) {
                if ($session->session_id === $cookies['Bazaari']) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function markMessengerAway($resourceId)
    {
        //if someone closes their browser and hits refresh.
        $account = $this->basepackages->accounts->checkAccountByNotificationsTunnelId($resourceId);

        if ($account) {
            $this->accountsObj->tunnels->assign(['notifications_tunnel'  => null])->update();
            // $account['notifications_tunnel_id'] = null;

            // $this->basepackages->accounts->update($account);

            $profile = $this->basepackages->profile->getProfile($account['id']);

            $messenger = new Messenger();

            if (isset($profile['settings']['messenger']['status'])) {
                if ($profile['settings']['messenger']['status'] == 4) {
                    return;
                }
            }

            $messenger->changeStatus(['user' => $account['id'], 'status' => 2]);
        }
    }
}