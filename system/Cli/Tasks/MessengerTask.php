<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use System\Base\Providers\WebSocketServiceProvider\WssOriginCheck;

class MessengerTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {

        $loop = Factory::create();

        $wsserver = new WsServer($this->basepackages->messenger->setCliLogger($this->logger));
        $wsserver->enableKeepAlive($loop);

        try {
            $domains = $this->domains->domains;
        } catch (\throwable $e) {
            $domains = [];
        }

        if ($this->config->setup === true || count($domains) === 0) {
            $originCheck = $wsserver;
        } else {
            $originCheck =
                new WssOriginCheck(
                    $wsserver,
                    [],
                    $this->logger,
                    $this->domains->domains
                );
        }

        $server = IoServer::factory(
            new HttpServer($originCheck),
            4443
        );

        try {
            $server->run();
        } catch (\Exception $e) {
            exit();//Need to handle errors properly
        }
    }
}