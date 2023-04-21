<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
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

        $allowedDomains = ['localhost'];

        foreach ($this->domains->domains as $domain) {
            array_push($allowedDomains, $domain['name']);
        }

        $wsserver = new WsServer($this->basepackages->messenger);
        $wsserver->enableKeepAlive($loop);

        $server = IoServer::factory(
            new HttpServer(
                new OriginCheck(
                    $wsserver
                ),
                $allowedDomains
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