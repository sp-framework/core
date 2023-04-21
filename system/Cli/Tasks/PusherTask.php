<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;

class PusherTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        $this->checkLogPath();

        $allowedDomains = ['localhost'];

        foreach ($this->domains->domains as $domain) {
            array_push($allowedDomains, $domain['name']);
        }

        $loop = Factory::create();

        $context = new Context($loop);

        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555');
        $pull->on('message', array($this->basepackages->pusher, 'onNewPush'));

        $webSock = new Server('0.0.0.0:4444', $loop);

        try {
            $webServer = new IoServer(
                new HttpServer(
                    new OriginCheck(
                        new WsServer(
                            new WampServer(
                                $this->basepackages->pusher
                            )
                        ),
                        $allowedDomains
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

    protected function checkLogPath()
    {
        if (!is_dir(base_path('var/log/'))) {
            if (!mkdir(base_path('var/log/'), 0777, true)) {
                return false;
            }
        }

        if (!file_exists(base_path('var/log/pusher-info.log'))) {
            $file = fopen(base_path('var/log/pusher-info.log'));
            fclose($file);
        }

        if (!file_exists(base_path('var/log/pusher-error.log'))) {
            $file = fopen(base_path('var/log/pusher-error.log'));
            fclose($file);
        }

        return true;
    }
}