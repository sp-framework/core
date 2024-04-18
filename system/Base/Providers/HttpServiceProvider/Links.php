<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Mvc\Url;

class Links
{
	protected $request;

	protected $app;

	protected $view;

	protected $domain;

	public $phalconUrl;

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
		$this->phalconUrl = new Url();

		$this->phalconUrl->setBasePath(base_path(''));

		$this->phalconUrl->setBaseUri('/');

		$this->phalconUrl->setStaticBaseUri(
			$this->request->getScheme() . '://' . $this->request->getHttpHost() . '/'
		);

		return $this;
	}

	public function url($link = null, $excludeApp = null)
	{
		if ($link) {
			// if ($link === '/') {
			// 	return '/';
			// }

			$link = ltrim($link, '/');

			if ((isset($this->domain['exclusive_to_default_app']) &&
				$this->domain['exclusive_to_default_app'] == 1) ||
				$excludeApp
			) {
				return $this->phalconUrl->getStatic('/' . $link);
			}

			return $this->phalconUrl->getStatic(
				strtolower($this->app['route']) . '/' . $link
			);

		} else {
			return $this->phalconUrl->getStatic('/');
		}
	}

	public function images($link)
	{
		return $this->phalconUrl->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/images/' . $link);
	}

	public function css($link)
	{
		return $this->phalconUrl->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/css/' . $link);
	}

	public function js($link)
	{
		return $this->phalconUrl->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/js/' . $link);
	}

	public function fonts($link)
	{
		return $this->phalconUrl->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/fonts/' . $link);
	}

	public function sounds($link)
	{
		return $this->phalconUrl->getStatic(
			$this->app['app_type'] . '/' .
			strtolower($this->view['name']) . '/sounds/' . $link);
	}
}