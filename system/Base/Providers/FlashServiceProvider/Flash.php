<?php

namespace System\Base\Providers\FlashServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Escaper;
use Phalcon\Flash\Direct;

class Flash
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

	public function init()
	{
		$escaper = new Escaper();

		$flash = new Session($escaper);

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