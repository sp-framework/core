<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Url;

class Links
{
	protected $request;

	protected $application;

	protected $view;

	protected $url;

	public function __construct($request, $application, $view)
	{
		$this->request = $request;

		$this->application = $application;

		$this->view = $view;

		$this->init();
	}

	public function init()
	{
		$this->url = new Url();

		$this->url->setBasePath(base_path(''));

		$this->url->setBaseUri('/');

		$this->url->setStaticBaseUri(
			$this->request->getScheme() . '://' . $this->request->getHttpHost() . '/'
		);
	}

	public function url($link = null)
	{
		if ($link) {
			return $this->url->getStatic(
				strtolower($this->application['name']) . '/' . $link
			);
		} else {
			return $this->url->getStatic('/');
		}
	}

	public function images($link)
	{
		return $this->url->getStatic(
			ucfirst($this->application['name']) . '/' .
			ucfirst($this->view['name']) . '/images/' . $link);
	}

	public function css($link)
	{
		return $this->url->getStatic(
			ucfirst($this->application['name']) . '/' .
			ucfirst($this->view['name']) . '/css/' . $link);
	}

	public function js($link)
	{
		return $this->url->getStatic(
			ucfirst($this->application['name']) . '/' .
			ucfirst($this->view['name']) . '/js/' . $link);
	}

	public function fonts($link)
	{
		return $this->url->getStatic(
			ucfirst($this->application['name']) . '/' .
			ucfirst($this->view['name']) . '/fonts/' . $link);
	}

	public function sounds($link)
	{
		return $this->url->getStatic(
			ucfirst($this->application['name']) . '/' .
			ucfirst($this->view['name']) . '/sounds/' . $link);
	}
}