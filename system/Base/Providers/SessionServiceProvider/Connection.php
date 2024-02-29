<?php

namespace System\Base\Providers\SessionServiceProvider;

use Phalcon\Support\Helper\Str\Random;

class Connection
{
	protected $connectionId;

	protected $helper;

	public function __construct($helper)
	{
		$this->helper = $helper;
	}

	public function init()
	{
		return $this;
	}

	public function getId()
	{
		if (!$this->connectionId) {
			$this->connectionId = $this->helper->random(Random::RANDOM_ALNUM);
		}

		return $this->connectionId;
	}
}