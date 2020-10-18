<?php

namespace System\Base\Providers\FlashServiceProvider;

use Phalcon\Escaper;
use Phalcon\Flash\Session;

class FlashSession
{
	public function __construct()
	{
	}

	public function init()
	{
		$escaper = new Escaper();

		$flashSession = new Session($escaper);

		$cssClasses = [
			'error'   	=> 'alert alert-danger',
			'success' 	=> 'alert alert-success',
			'notice'  	=> 'alert alert-info',
			'warning' 	=> 'alert alert-warning'
		];

		$flashSession->setCssClasses($cssClasses);

		//BS4
		$template = '<span class="%cssClass%">%message%</span>';

		$flashSession->setCustomTemplate($template);

		return $flashSession;
	}
}