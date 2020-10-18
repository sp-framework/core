<?php

namespace System\Base\Providers\SessionServiceProvider;

use Phalcon\Helper\Str;

class Connection
{
	protected $connectionId;

	public function __construct()
	{
	}

	public function init()
	{
		return $this;
	}

	public function getId()
	{
		if (!$this->connectionId) {
			$this->connectionId = Str::random(Str::RANDOM_ALNUM);
		}

		return $this->connectionId;
	}
}