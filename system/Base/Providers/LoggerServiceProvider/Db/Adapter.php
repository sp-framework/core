<?php

namespace System\Base\Providers\LoggerServiceProvider\Db;

use Phalcon\Di\DiInterface;
use Phalcon\Logger\Adapter\AbstractAdapter;
use Phalcon\Logger\Item;
use System\Base\Providers\LoggerServiceProvider\Db\Logs;

class Adapter extends AbstractAdapter
{
    protected $logs;

    protected $messages = [];

    protected $oneDbEntry;//True to make only 1 DB Entry

    protected $helper;

    public function __construct($oneDbEntry, $helper)
    {
        $this->logs = new Logs;

        $this->oneDbEntry = $oneDbEntry;

        $this->helper = $helper;
    }

    public function process(Item $item): void
    {
        if (!$this->oneDbEntry) {
            $message = $this->helper->decode($this->formatter->format($item), true);

            $this->logs->add($message);

        } else if ($this->oneDbEntry) {

            $this->messages[] = $this->helper->decode($this->formatter->format($item), true);
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
            $data['message'] .= $this->helper->encode($msg);
        }

        $this->logs->add($data);
    }
}