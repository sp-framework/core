<?php

namespace Apps\Dash\Packages\System\Messenger;

use Apps\Dash\Packages\System\Messenger\Model\SystemMessenger;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Helper\Json;
use Phalcon\Storage\SerializerFactory;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use System\Base\BasePackage;
use ZMQContext;

class Messenger extends BasePackage implements MessageComponentInterface
{
    protected $modelToUse = SystemMessenger::class;

    protected $packageName = 'messenger';

    protected $clients;

    protected $socket;

    protected $account;

    public function onConstruct()
    {
        $this->clients = new \SplObjectStorage;

        $this->initSocket();

        parent::onConstruct();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        if ($this->checkAccount($conn)) {
            $this->clients->attach($conn);

            // $conn->send();
        } else {
            $conn->close();
        }
        // Store the new connection to send messages to later

        echo "New connection! ({$conn->resourceId})\n";
    }

    //Off The Record Messages
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $message = Json::decode($msg, true);

        var_dump($from->resourceId, $msg);
        // if (isset($message['changeStatus'])) {
        //     return;
        // }
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
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
            $this->account['messenger_tunnel_id'] = $conn->resourceId;

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

    public function changeStatus(array $data)
    {
        if (isset($data['user'])) {
            $account = $this->basepackages->accounts->getById($data['user']);
        } else {
            $account = $this->auth->account();
        }

        $profile = $this->basepackages->profile->getProfile($account['id']);

        if ($profile) {
            if (isset($profile['settings']['messenger'])) {
                $profile['settings']['messenger']['status'] = $data['status'];
            } else {
                $profile['settings']['messenger'] = [];
                $profile['settings']['messenger']['status'] = $data['status'];
            }

            $profile['settings'] = Json::encode($profile['settings']);

            $this->basepackages->profile->update($profile);

            $this->pushNotification(
                'messengerNotifications',
                $account['notifications_tunnel_id'],
                null,
                true,
                [
                    'responseCode'      => 0,
                    'responseData'      =>
                        [
                            "type"  => 'statusChange',
                            "data"  =>
                                [
                                    'id'        => $account['id'],
                                    'status'    => $data['status']
                                ]
                        ]
                ]
            );

            $this->addResponse('Status Changed');

            return;
        }

        $this->addResponse('Could not modify status', 1);
    }

    protected function initSocket()
    {
        $context = new ZMQContext();

        $this->socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'New Notification');
        $this->socket->connect("tcp://localhost:5555");
    }

    protected function pushNotification($type, $from, $to, bool $broadcast = null, array $data)
    {
        $message =
            [
                'type'              => $type,
                'from'              => $from,
                'to'                => $to,
                'broadcast'         => $broadcast,
                'response'          => $data
            ];

        $this->socket->send(Json::encode($message));
    }

    public function getMessages(array $data)
    {
        $conditions =
            [
                'conditions'    =>
                    '-:from_account_id:equals:' . $data['user'] . '&and:to_account_id:equals:' . $this->auth->account()['id'] . '&' .
                    'or:from_account_id:equals:' . $this->auth->account()['id'] . '&and:to_account_id:equals:' . $data['user'] . '&',
                'order'         => 'id desc'
            ];

        $pagedData = $this->getPaged($conditions);

        if ($pagedData) {
            if (count($pagedData->getItems()) > 0) {
                $data['messages'] = array_reverse($pagedData->getItems());

                $data['paginationCounters'] = $this->packagesData->paginationCounters;

                $this->addResponse('Retrieved Messages.', 0, $data);
            } else {
                $data['messages'] = [];

                $this->addResponse('No messages for this user.', 0, $data);
            }
        } else {
            $this->addResponse('Error retrieving messages.', 1);
        }
    }

    public function getUnreadMessagesCount()
    {
        $conditions =
            [
                'conditions'    => 'to_account_id = ' . $this->auth->account()['id'] . ' AND unread = 1'
            ];

        $total = $this->modelToUse::count($conditions);

        $membersArr = $this->basepackages->profile->getProfile($this->auth->account()['id'])['settings']['messenger']['members'];

        $members = [];

        foreach ($membersArr['users'] as $key => $memberId) {
            $members[$key]['id'] = $memberId;
            $conditions =
                [
                    'conditions'    => 'to_account_id = ' . $this->auth->account()['id'] . ' AND from_account_id = ' . $memberId . ' AND unread = 1'
                ];

            $members[$key]['count'] = $this->modelToUse::count($conditions);
        }

        if ($total >= 0 && $members) {
            $data['total'] = $total;

            $data['unread_count'] = $members;

            $this->addResponse('Retrieved messages count.', 0, $data);
        } else {
            $this->addResponse('Error retrieving messages count.', 1);
        }
    }

    public function markAllMessagesRead(array $data)
    {
        $conditions =
            [
                'conditions'    => 'to_account_id = ' . $this->auth->account()['id'] . ' AND from_account_id = ' . $data['user'] . ' AND unread = 1'
            ];

        $messages = $this->getByParams($conditions);

        foreach ($messages as $key => $message) {
            $message['unread'] = 0;

            $this->update($message);
        }

        $this->addResponse('Marked all messaged read.');
    }

    public function addMessage(array $data)
    {
        $messageData['from_account_id'] = $this->auth->account()['id'];
        $messageData['to_account_id'] = $data['user'];
        $messageData['message'] = $data['message'];

        $toAccount = $this->basepackages->accounts->getById($data['user']);

        if ($toAccount) {
            $toProfile = $this->basepackages->profile->getProfile($data['user']);

            $offline = false;

            if (isset($toProfile['settings']['messenger']['status'])) {
                if ($toProfile['settings']['messenger']['status'] == 4) {
                    $messageData['unread'] = 1;
                    $offline = true;
                } else if ($toProfile['settings']['messenger']['status'] == 2) {
                    $messageData['unread'] = 1;
                }
            } else {
                $offline = true;
                $messageData['unread'] = 1;
            }
        } else {
            $this->addResponse('User not found', 1);
            return;
        }

        if ($this->add($messageData)) {
            $this->addResponse('Message Added', 0);

            if ($offline) {
                return;
            }

            $profile = $this->basepackages->profile->getProfile($this->auth->account()['id']);

            $userData['id'] = $profile['id'];
            $userData['portrait'] = $profile['portrait'];
            $userData['name'] = $profile['full_name'];
            $userData['status'] = $profile['settings']['messenger']['status'];

            $this->pushNotification(
                'messengerNotifications',
                $this->auth->account()['notifications_tunnel_id'],
                $toAccount['notifications_tunnel_id'],
                false,
                [
                    'responseCode'      => 0,
                    'responseData'      =>
                        [
                            "type"  => 'newMessage',
                            "data"  =>
                                [
                                    'user'      => $userData,
                                    'message'   => $this->packagesData->last
                                ]
                        ]
                ]
            );
        } else {
            $this->addResponse('Error adding message', 1);

            return;
        }
    }

    public function updateMessage(array $data)
    {
        $messageData = $this->getById($data['id']);

        $messageData['message'] = $data['message'];
        $messageData['updated_at'] = date("Y-m-d H:i:s");

        if ($this->update($messageData)) {
            $this->addResponse('Message Updated', 0);
        } else {
            $this->addResponse('Error updating message', 1);
        }
    }

    public function removeMessage(array $data)
    {
        $messageData = $this->getById($data['id']);

        $messageData['message'] = 'Message Removed';
        $messageData['removed'] = 1;
        $messageData['updated_at'] = date("Y-m-d H:i:s");

        if ($this->update($messageData)) {
            $this->addResponse('Message Removed', 0);
        } else {
            $this->addResponse('Error removing message', 1);
        }
    }

    public function changeSettings(array $data)
    {
        $profile = $this->basepackages->profile->profile();

        if (isset($profile['settings']['messenger']['mute'])) {
            if ($data['changestate'] == 1) {
                $profile['settings']['messenger']['mute'] = true;
            } else if ($data['changestate'] == 0) {
                $profile['settings']['messenger']['mute'] = false;
            }
        } else {
            if ($data['changestate'] == 1) {
                $profile['settings']['messenger']['mute'] = true;
            } else if ($data['changestate'] == 0) {
                $profile['settings']['messenger']['mute'] = false;
            }
        }

        $profile['settings'] = Json::encode($profile['settings']);

        $this->basepackages->profile->updateProfile($profile);

        $this->addResponse('Changed');
    }
}