<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;

class PusherTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        $loop = Factory::create();

        $context = new Context($loop);

        $pusher = $this->basepackages->pusher;

        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555');
        $pull->on('message', array($pusher, 'onNewPush'));

        $webSock = new Server('0.0.0.0:4444', $loop);

        try {
            $webServer = new IoServer(
                new HttpServer(
                    new WsServer(
                        new WampServer(
                            $pusher
                        )
                    )
                ),
                $webSock
            );

            $loop->run();
        } catch (\Exception $e) {
            var_dump($e);
            // exit();//Need to handle errors properly
        }
    }
}