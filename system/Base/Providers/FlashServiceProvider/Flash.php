<?php

namespace System\Base\Providers\FlashServiceProvider;

use Phalcon\Flash\Direct;
use Phalcon\Html\Escaper;

class Flash
{
	public function __construct()
	{
	}

	public function init()
	{
		$escaper = new Escaper();

		$flash = new Direct($escaper);

		$cssClasses = [
			'error'   	=> 'alert alert-danger',
			'success' 	=> 'alert alert-success',
			'notice'  	=> 'alert alert-info',
			'warning' 	=> 'alert alert-warning'
		];

		$flash->setCssClasses($cssClasses);

		//BS4
		$template = '<span class="%cssClass%">%message%</span>';

		$flash->setCustomTemplate($template);

		return $flash;
	}
}