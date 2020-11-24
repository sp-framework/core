<?php

namespace System\Base\Providers\BasepackagesServiceProvider;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Domains;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Filters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Menus;

class Basepackages
{
	protected $filters;

	protected $domains;

	protected $menus;

	protected $businesses;

	protected $channels;

	protected $currencies;

	protected $taxes;

	public function __construct()
	{
	}

	public function __get($name)
	{
		if (!isset($this->{$name})) {
			if (method_exists($this, $method = "init" . ucfirst("{$name}"))) {
				$this->{$name} = $this->{$method}();
			}
		}

		return $this->{$name};
	}

	protected function initFilters()
	{
		$this->filters = (new Filters())->init();

		return $this->filters;
	}

	protected function initDomains()
	{
		$this->domains = (new Domains())->init();

		return $this->domains;
	}

	protected function initMenus()
	{
		$this->menus = (new Menus())->init();

		return $this->menus;
	}
}