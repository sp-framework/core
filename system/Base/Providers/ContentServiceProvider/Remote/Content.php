<?php

namespace System\Base\Providers\ContentServiceProvider\Remote;

use GuzzleHttp\Client;

class Content
{
	public function __construct()
	{
		include (__DIR__ . '/vendor/autoload.php');
	}

	public function init()
	{
		return new Client();
	}
}