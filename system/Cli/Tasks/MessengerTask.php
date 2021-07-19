<?php

namespace System\Cli\Tasks;

use Apps\Dash\Packages\System\Messenger\Messenger;
use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class MessengerTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Messenger()
                )
            ),
            4433
        );

        try {
            $server->run();
        } catch (\Exception $e) {
            exit();//Need to handle errors properly
        }
    }
}