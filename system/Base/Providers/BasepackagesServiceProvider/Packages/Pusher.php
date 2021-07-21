<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use System\Base\BasePackage;

class Pusher extends BasePackage implements WampServerInterface
{
    protected $subscriptions = [];

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
        $this->markMessengerOffline($conn->resourceId);
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

        $this->account = $this->basepackages->accounts->getById($cookies['id']);

        if (!$this->account) {
            return false;
        }

        $this->account['profile'] = $this->basepackages->profile->getProfile($this->account['id']);

        if ($this->checkSession($cookies)) {
            $this->account['notifications_tunnel_id'] = $conn->resourceId;

            $this->basepackages->accounts->update($this->account);

            return true;
        }

        return false;
    }

    protected function checkSession($cookies)
    {
        if (isset($this->account['session_ids'])) {
            if (!is_array($this->account['session_ids'])) {
                $apps = Json::decode($this->account['session_ids'], true);
            }

            foreach ($apps as $appKey => $sessions) {
                if (in_array($cookies['Bazaari'], $sessions)) {
                    return true;
                }
            }
        } else {
            return false;
        }

        return false;
    }

    protected function markMessengerOffline($resourceId)
    {
        $account = $this->basepackages->accounts->checkAccountByNotificationsTunnelId($resourceId);

        if ($account) {
            $account['notifications_tunnel_id'] = null;

            $this->basepackages->accounts->update($account);

            $messenger = new \Apps\Dash\Packages\System\Messenger\Messenger();

            $messenger->changeStatus(['user' => $account['id'], 'status' => 4]);
        }
    }
}