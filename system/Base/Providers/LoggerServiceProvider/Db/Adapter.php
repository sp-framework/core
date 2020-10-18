<?php

namespace System\Base\Providers\LoggerServiceProvider\Db;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Json;
use Phalcon\Logger\Adapter\AbstractAdapter;
use Phalcon\Logger\Item;
use System\Base\Providers\LoggerServiceProvider\Db\Logs;

class Adapter extends AbstractAdapter
{
    protected $logs;

    protected $messages = [];

    protected $oneDbEntry;//True to make only 1 DB Entry

    public function __construct($oneDbEntry)
    {
        $this->logs = new Logs;

        $this->oneDbEntry = $oneDbEntry;
    }

    public function process(Item $item): void
    {
        if (!$this->oneDbEntry) {
            $message = Json::decode($this->formatter->format($item), true);

            $this->logs->add($message);

        } else if ($this->oneDbEntry) {

            $this->messages[] = Json::decode($this->formatter->format($item), true);
        }
    }

    public function close(): bool
    {
        return true;
    }

    public function addToDb()
    {
        $data = [];
        $data['type'] = 8;
        $data['type_name'] = 'bulk';
        $data['client_ip'] = $this->messages[0]['client_ip'];
        $data['session'] = $this->messages[0]['session'];
        $data['connection'] = $this->messages[0]['connection'];
        $data['mseconds'] = $this->messages[0]['mseconds'];
        $data['message'] = '';

        foreach ($this->messages as $msg) {
            $data['message'] .= Json::encode($msg);
        }

        $this->logs->add($data);
    }
}