<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

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

    protected $account;

    protected $appRoute;

    public function init()
    {
        $this->tunnelsObj = new BasepackagesUsersAccountsTunnels;

        return $this;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->subscriptions[$topic->getId()] = $topic;

        $this->opCache->setCache('pusherSubscriptions', $this->subscriptions);
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
        $this->markPusherAway($conn->resourceId);
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

    public function getSubscriptions()
    {
        $this->opCache->getCache('pusherSubscriptions');
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

            if ($this->config->setup && $this->config->setup === false) {
                foreach ($topic->getIterator() as $key => $connection) {
                    if ($connection->resourceId != $newPush['to']) {
                        array_push($excludeUsers, $connection->WAMP->sessionId);
                    } else if ($connection->resourceId == $newPush['to']) {
                        array_push($eligibleUsers, $connection->WAMP->sessionId);
                    }
                }
            } else {
                if ($newPush['type'] === 'progress') {
                    $installerResourceId = $this->opCache->getCache('InstallerResourceId');

                    if ($installerResourceId) {
                        foreach ($topic->getIterator() as $key => $connection) {
                            array_push($eligibleUsers, $connection->WAMP->sessionId);
                        }
                    }
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

        return true;
    }

    protected function checkAccount($conn)
    {
        $pathArr = explode('/', ltrim(rtrim($conn->httpRequest->getUri()->getPath(), '/'), '/'));

        if (count($pathArr) === 2) {
            if (strtolower($pathArr[0]) === 'app') {
                $this->appRoute = strtolower($pathArr[1]);
            }
        } else {
            $this->logger->log->debug('Disconnect as we didn\'t receive app route');
            return false;//Disconnect as we didnt receive appRoute
        }

        $app = $this->apps->getAppInfo($this->appRoute);

        if (!$app) {
            $this->logger->log->debug('Disconnect as app not found');
            return false;//App not found
        }

        $this->apps->ipFilter->setClientAddress($conn->httpRequest->getHeaders()['X-Forwarded-For'][0]);
        if (!$this->apps->ipFilter->checkList()) {//IP Is Blocked
            $this->logger->log->debug($conn->httpRequest->getHeaders()['X-Forwarded-For'][0] . ' IP is blocked.');
            return false;
        }
        // $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

        $cookiesArr = [];
        $cookies = [];

        if (isset($conn->httpRequest->getHeaders()['Cookie'][0])) {
            $cookiesArr = $conn->httpRequest->getHeaders()['Cookie'][0];
            $cookiesArr = explode(';', $cookiesArr);

            foreach ($cookiesArr as $cookie) {
                $cookie = explode('=', $cookie);
                $cookies[trim($cookie[0])] = trim($cookie[1]);
            }
        } else {
            //Someone trying to connect without proper cookies
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            $this->logger->log->debug($conn->httpRequest->getHeaders()['X-Forwarded-For'][0] . ' Cookie misuse. disconnected websocket.');
            return false;
        }

        if (!isset($cookies['Bazaari'])) {
            $this->apps->ipFilter->bumpFilterHitCounter(null, false, true);

            $this->logger->log->debug($conn->httpRequest->getHeaders()['X-Forwarded-For'][0] . ' Bazaari Cookie not set. disconnected websocket.');
            return false;
        }

        //For Installer Progress
        if (isset($cookies['Installer']) &&
            $cookies['Installer'] === $cookies['Bazaari']
        ) {
            $this->opCache->setCache('InstallerResourceId', $conn->resourceId);

            return true;
        }

        if (isset($cookies['id'])) {
            $this->db->connect();

            $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($cookies['id']);

            if ($this->accountsObj && $this->accountsObj->count() > 0) {
                $this->account = $this->accountsObj->toArray();
            }

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
        }

        $this->logger->log->debug($conn->httpRequest->getHeaders()['X-Forwarded-For'][0] . ' ID Cookie not set. disconnected websocket.');

        return false;
    }

    protected function checkSession($cookies)
    {
        if ($this->accountsObj && $this->accountsObj->sessions) {
            foreach ($this->accountsObj->sessions as $key => $session) {
                if ($session->session_id === $cookies['Bazaari']) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function markPusherAway($resourceId)
    {
        //if someone closes their browser and hits refresh.
        $account = $this->basepackages->accounts->checkAccountByNotificationsTunnelId($resourceId);

        if ($account) {
            $this->accountsObj->tunnels->assign(['notifications_tunnel'  => null])->update();
            // $account['notifications_tunnel_id'] = null;

            // $this->basepackages->accounts->update($account);

            $profile = $this->basepackages->profile->getProfile($account['id']);

            $messenger = $this->basepackages->messenger;

            if (isset($profile['settings']['messenger']['status'])) {
                if ($profile['settings']['messenger']['status'] == 4) {
                    return;
                }
            }

            $messenger->changeStatus(['user' => $account['id'], 'status' => 2]);
        }
    }
}