<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;

class MessengerTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        $loop = Factory::create();

        $wsserver = new WsServer($this->basepackages->messenger);
        $wsserver->enableKeepAlive($loop);

        $server = IoServer::factory(
            new HttpServer(
                $wsserver
            ),
            4443
        );

        try {
            $server->run();
        } catch (\Exception $e) {
            exit();//Need to handle errors properly
        }
    }
}