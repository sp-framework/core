<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Storage\SerializerFactory;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Messenger\BasepackagesMessenger;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\WebSocketServiceProvider\WebsocketBase;
use ZMQContext;

class Messenger extends WebsocketBase implements MessageComponentInterface
{
    protected $modelToUse = BasepackagesMessenger::class;

    protected $packageName = 'messenger';

    protected $clients;

    protected $socket;

    protected $account;

    protected $accountsObj;

    protected $tunnelsObj;

    protected $ffStore;

    protected $ffStoreToUse;

    protected $ffData;

    protected $ffRelations = false;

    protected $ffRelationsConditions = false;

    public function init()
    {
        $this->tunnelsObj = new BasepackagesUsersAccountsTunnels;

        $this->setFfStoreToUse();

        return $this;
    }

    protected function setFfStoreToUse()
    {
        $model = new $this->modelToUse;

        $this->ffStoreToUse = $model->getSource();

        $this->ffRelations = false;

        $this->ffRelationsConditions = false;
    }

    public function onConstruct()
    {
        $this->clients = new \SplObjectStorage;

        // $this->initSocket();

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
        // var_dump($from, $msg);die();
        // $message = $this->helper->decode($msg, true);

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

        $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($cookies['id']);

        $this->account = $this->accountsObj->toArray();

        if (!$this->account) {
            return false;
        }

        $this->account['profile'] = $this->basepackages->profiles->getProfile($this->account['id']);

        if ($this->checkSession($cookies)) {
            if (!$this->accountsObj->tunnels) {
                $newTunnel =
                    [
                        'account_id'            => $this->account['id'],
                        'messenger_tunnel'      => $conn->resourceId
                    ];


                $this->tunnelsObj->assign($newTunnel);

                $this->tunnelsObj->create();
            } else {
                $this->accountsObj->tunnels->assign(['messenger_tunnel' => $conn->resourceId])->update();
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

    public function changeStatus(array $data)
    {
        if (isset($data['user'])) {
            $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($data['user']);

            $account = $this->accountsObj->toArray();
        } else {
            $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($this->access->auth->account()['id']);

            $account = $this->access->auth->account();
        }

        $profile = $this->basepackages->profiles->getProfile($account['id']);

        if ($profile && $this->accountsObj->tunnels) {
            if (isset($profile['settings']['messenger'])) {
                $profile['settings']['messenger']['status'] = $data['status'];
            } else {
                $profile['settings']['messenger'] = [];
                $profile['settings']['messenger']['status'] = $data['status'];
            }

            $profile['settings'] = $this->helper->encode($profile['settings']);

            $this->basepackages->profiles->update($profile);

            $this->pushNotification(
                'messengerNotifications',
                $this->accountsObj->tunnels->notifications_tunnel,
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

        $this->addResponse('Could not modify status, messenger service offline.', 1);
    }

    // protected function initSocket()
    // {
    //     $context = new ZMQContext();

    //     $this->socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'New Notification');
    //     $this->socket->connect("tcp://localhost:5555");
    // }

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

        $this->wss->send($message);
    }

    public function getMessages(array $data)
    {
        $conditions =
            [
                'conditions'    =>
                    '-|from_account_id|equals|' . $data['user'] . '&and|to_account_id|equals|' . $this->access->auth->account()['id'] . '&' .
                    'or|from_account_id|equals|' . $this->access->auth->account()['id'] . '&and|to_account_id|equals|' . $data['user'] . '&',
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
        $total = 0;

        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'to_account_id = ' . $this->access->auth->account()['id'] . ' AND read = 0'
                ];

            $total = $this->modelToUse::count($conditions);
        } else {
            $this->ffStore = $this->ff->store($this->ffStoreToUse);

            $this->ffData = $this->ffStore->findBy([['to_account_id', '=', $this->access->auth->account()['id']],['read', '=', 0]]);

            if ($this->ffData) {
                $total = count($this->ffData);
            }
        }

        $profile = $this->basepackages->profiles->getProfile($this->access->auth->account()['id']);

        if ($profile['settings'] && isset($profile['settings']['messenger']['members'])) {
            $membersArr = $profile['settings']['messenger']['members'];

            $members = [];

            foreach ($membersArr['users'] as $key => $memberId) {
                $members[$key]['id'] = $memberId;

                if ($this->config->databasetype === 'db') {
                    $conditions =
                        [
                            'conditions'    => 'to_account_id = ' . $this->access->auth->account()['id'] . ' AND from_account_id = ' . $memberId . ' AND read = 0'
                        ];

                    $members[$key]['count'] = $this->modelToUse::count($conditions);
                } else {
                    $this->ffStore = $this->ff->store($this->ffStoreToUse);

                    $this->ffData = $this->ffStore->findBy([['to_account_id', '=', $this->access->auth->account()['id']],['from_account_id', '=', (int) $memberId],['read', '=', 0]]);

                    if ($this->ffData) {
                        $members[$key]['count'] = count($this->ffData);
                    }
                }
            }
        } else {
            $members = [];
        }

        $data['total'] = $total;

        $data['unread_count'] = $members;

        $this->addResponse('Retrieved messages count.', 0, $data);

        return true;
    }

    public function markAllMessagesRead(array $data)
    {
        $conditions =
            [
                'conditions'    => 'to_account_id = ' . $this->access->auth->account()['id'] . ' AND from_account_id = ' . $data['user'] . ' AND read = 0'
            ];

        $messages = $this->getByParams($conditions);

        foreach ($messages as $key => $message) {
            $message['read'] = 1;

            $this->update($message);
        }

        $this->addResponse('Marked all messaged read.');
    }

    public function addMessage(array $data)
    {
        $messageData['from_account_id'] = $this->access->auth->account()['id'];
        $messageData['to_account_id'] = $data['user'];
        $messageData['message'] = $data['message'];

        $toAccountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($data['user']);

        $toAccount = $toAccountsObj->toArray();

        if ($toAccount) {
            $toProfile = $this->basepackages->profiles->getProfile($data['user']);

            $offline = false;

            if (isset($toProfile['settings']['messenger']['status'])) {
                if ($toProfile['settings']['messenger']['status'] == 4) {
                    $messageData['read'] = 0;
                    $offline = true;
                } else if ($toProfile['settings']['messenger']['status'] == 2) {
                    $messageData['read'] = 0;
                }
            } else {
                $offline = true;
                $messageData['read'] = 0;
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

            $this->accountsObj = $this->basepackages->accounts->getModelToUse()::findFirstById($this->access->auth->account()['id']);

            $profile = $this->basepackages->profiles->getProfile($this->access->auth->account()['id']);

            $userData['id'] = $profile['id'];
            $userData['portrait'] = $profile['portrait'];
            $userData['name'] = $profile['full_name'];
            $userData['status'] = $profile['settings']['messenger']['status'];

            $this->pushNotification(
                'messengerNotifications',
                $this->accountsObj->tunnels->notifications_tunnel,
                $toAccountsObj->tunnels->notifications_tunnel,
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
        $profile = $this->basepackages->profiles->profile();

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

        $profile['settings'] = $this->helper->encode($profile['settings']);

        $this->basepackages->profiles->updateProfile($profile);

        $this->addResponse('Changed');
    }
}