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

    protected $account;

    public function onConstruct()
    {
        $this->clients = new \SplObjectStorage;

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
        $profile = $this->basepackages->profile->getProfile($this->auth->account()['id']);

        if ($profile) {
            if (isset($profile['settings']['messenger'])) {
                $profile['settings']['messenger']['status'] = $data['status'];
            } else {
                $profile['settings']['messenger'] = [];
                $profile['settings']['messenger']['status'] = $data['status'];
            }

            $profile['settings'] = Json::encode($profile['settings']);

            $this->basepackages->profile->update($profile);

            $this->pushNotification(['id' => $this->auth->account()['id'], 'status' => $data['status']]);

            $this->addResponse('Ok');
        }

        $this->addResponse('Could not modify status', 1);
    }

    protected function pushNotification($notification)
    {
        $context = new ZMQContext();

        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'New Notification');
        $socket->connect("tcp://localhost:5555");

        $data =
            [
                'type'              => 'messengerNotifications',
                'broadcast'         => true,
                'from'              => $this->auth->account()['notifications_tunnel_id'],
                'response'          => [
                    'responseCode'      => 0,
                    'responseData'      =>
                        [
                            "type"  => 'statusChange',
                            "data"  => $notification
                        ]
                ]
            ];

        $socket->send(Json::encode($data));
    }

    public function getMessages(array $data)
    {
        //
    }

    public function addMessage(array $data)
    {
        $messageData['from_account_id'] = $this->auth->account()['id'];
        $messageData['to_account_id'] = $data['user'];
        $messageData['message'] = $data['message'];

        if ($this->add($messageData)) {
            $this->addResponse('Message Added', 0);
        } else {
            $this->addResponse('Error adding message', 1);
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
        $messageData['updated_at'] = date("Y-m-d H:i:s");

        if ($this->update($messageData)) {
            $this->addResponse('Message Removed', 0);
        } else {
            $this->addResponse('Error removing message', 1);
        }
    }
}