<?php

namespace System\Cli\Tasks;

use Phalcon\Cli\Task;
use React\EventLoop\Loop;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use System\Base\Providers\WebSocketServiceProvider\WssOriginCheck;

class MessengerTask extends Task
{
    public function mainAction()
    {
        echo "you hit chat task main action, nothing to do\n";
    }

    public function startAction()
    {
        $this->checkLogPath();

        $loop = Loop::get();

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
                    $this->logger,
                    $this->domains->domains,
                    []
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

    protected function checkLogPath()
    {
        if (!is_dir(base_path('var/log/'))) {
            if (!mkdir(base_path('var/log/'), 0777, true)) {
                return false;
            }
        }

        if (!file_exists(base_path('var/log/messenger-info.log'))) {
            $file = fopen(base_path('var/log/messenger-info.log'), 'a+');
            fclose($file);
        }

        if (!file_exists(base_path('var/log/messenger-error.log'))) {
            $file = fopen(base_path('var/log/messenger-error.log'), 'a+');
            fclose($file);
        }

        return true;
    }
}