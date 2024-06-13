<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Logger\Formatter\AbstractFormatter;
use Phalcon\Logger\Item;

class CustomFormat extends AbstractFormatter
{
	protected $clientIpAddress;

	protected $sessionId;

	protected $connectionId;

	protected $dateFormat;

	protected $helper;

	public function __construct($clientIpAddress, $sessionId, $connectionId, $helper, string $dateFormat = 'c')
	{
		$this->clientIpAddress = $clientIpAddress;

		$this->sessionId = $sessionId;

		$this->connectionId = $connectionId;

		$this->dateFormat = $dateFormat;

		$this->helper = $helper;
	}

	public function format(Item $item) : string
	{
		try {
			$message = $this->helper->decode($item->getMessage());
		} catch (\Exception $e) {
			$message = $item->getMessage();
		}

		$toLog =
				[
					"type"      	=> $item->getLevel(),
					"typeName"     	=> $item->getLevelName(),
					"message"   	=> $message,
					"session"   	=> $this->sessionId,
					"connection" 	=> $this->connectionId,
					"client_ip"		=> $this->clientIpAddress,
					"timestamp" 	=> $this->getFormattedDate($item),
					"mseconds" 		=> microtime()
				];

		return $this->helper->encode($toLog);
	}
}