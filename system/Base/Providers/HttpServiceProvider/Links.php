<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Url;

class Links
{
	protected $request;

	protected $app;

	protected $view;

	protected $domain;

	protected $url;

	public function __construct($request, $app, $view, $domain)
	{
		$this->request = $request;

		$this->app = $app;

		$this->view = $view;

		$this->domain = $domain;

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
			if ($link === '/') {
				return '/';
			}

			if (isset($this->domain['exclusive_to_default_app']) &&
				$this->domain['exclusive_to_default_app'] == 1
			) {
				return $this->url->getStatic('/' . $link);
			}
			return $this->url->getStatic(
				strtolower($this->app['route']) . '/' . $link
			);
		} else {
			return $this->url->getStatic('/');
		}
	}

	public function images($link)
	{
		return $this->url->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/images/' . $link);
	}

	public function css($link)
	{
		return $this->url->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/css/' . $link);
	}

	public function js($link)
	{
		return $this->url->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/js/' . $link);
	}

	public function fonts($link)
	{
		return $this->url->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/fonts/' . $link);
	}

	public function sounds($link)
	{
		return $this->url->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/sounds/' . $link);
	}
}