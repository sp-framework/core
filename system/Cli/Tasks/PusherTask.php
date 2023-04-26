<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use System\Base\Providers\WebSocketServiceProvider\WssOriginCheck;

class PusherTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        if ($this->config->setup === true) {
            $originCheck =
                new WsServer(
                    new WampServer(
                        $this->basepackages->pusher->setCliLogger($this->logger)
                    )
                );
        } else {
            $originCheck =
                new WssOriginCheck(
                    new WsServer(
                        new WampServer(
                            $this->basepackages->pusher->setCliLogger($this->logger)
                        )
                    ),
                    [],
                    $this->logger,
                    $this->domains->domains
                );
        }

        $this->checkLogPath();

        $loop = Factory::create();

        $context = new Context($loop);

        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555');
        $pull->on('message', array($this->basepackages->pusher, 'onNewPush'));

        $webSock = new Server('0.0.0.0:4444', $loop);

        try {
            $webServer = new IoServer(
                new HttpServer($originCheck),
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