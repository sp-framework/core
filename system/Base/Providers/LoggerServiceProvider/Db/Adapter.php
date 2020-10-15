<?php

namespace System\Base\Providers\LoggerServiceProvider\Db;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Json;
use Phalcon\Logger\Adapter\AbstractAdapter;
use Phalcon\Logger\Adapter\AdapterInterface;
use Phalcon\Logger\Formatter\FormatterInterface;
use Phalcon\Logger\Item;
use System\Base\Providers\LoggerServiceProvider\Db\Logs;

class Adapter extends AbstractAdapter
{
    protected $container;

    protected $logs;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->logs = new Logs($container);
    }

    public function process(Item $item): void
    {
        $message = Json::decode($this->formatter->format($item), true);

        $this->logs->add($message);
    }

    public function close(): bool
    {
        return true;
    }
}