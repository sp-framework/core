<?php

namespace System\Base\Providers\LoggerServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use Phalcon\Logger\Formatter\AbstractFormatter;
use Phalcon\Logger\Item;

class CustomFormat extends AbstractFormatter
{
	protected $dateFormat;

	public $connectionId;

	public function __construct(string $dateFormat = 'c', $sessionId, $debug)
	{
		$this->dateFormat = $dateFormat;

		$this->sessionId = $sessionId;

		$this->debug = $debug;
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
					"connection" 	=> $this->addConnectionId(),
					"timestamp" 	=> $this->getFormattedDate(),
					"mseconds" 		=> microtime()
				];

		return Json::encode($toLog);
	}

	public function addConnectionId()
	{
		if (!$this->connectionId) {
			$this->connectionId = Str::random(Str::RANDOM_ALNUM);
		}

		return $this->connectionId;
	}

	public function getConnectionId()
	{
		return $this->addConnectionId();
	}
}