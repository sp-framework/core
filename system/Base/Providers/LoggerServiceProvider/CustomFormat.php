<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use Phalcon\Logger\Formatter\AbstractFormatter;
use Phalcon\Logger\Item;

class CustomFormat extends AbstractFormatter
{
	protected $dateFormat;

	protected $sessionId;

	protected $connectionId;

	protected $clientIpAddress;

	public function __construct(string $dateFormat = 'c', $sessionId, $connectionId, $clientIpAddress)
	{
		$this->dateFormat = $dateFormat;

		$this->sessionId = $sessionId;

		$this->clientIpAddress = $clientIpAddress;
	}

	public function format(Item $item) : string
	{
		if (is_array($item->getContext())) {
			$message = $this->interpolate(
				$item->getMessage(),
				$item->getContext()
			);
		} else {
			$message = $item->getMessage();
		}

		$toLog =
				[
					"type"      	=> $item->getType(),
					"typeName"     	=> $item->getName(),
					"message"   	=> $message,
					"session"   	=> $this->sessionId,
					"connection" 	=> $this->connectionId,
					"client_ip"		=> $this->clientIpAddress,
					"timestamp" 	=> $this->getFormattedDate(),
					"mseconds" 		=> microtime()
				];

		return Json::encode($toLog);
	}
}