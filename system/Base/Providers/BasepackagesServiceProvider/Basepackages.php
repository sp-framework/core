<?php

namespace System\Base\Providers\BasepackagesServiceProvider;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Addressbook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Domains;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Filters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCities;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCountries;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoStates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Menus;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class Basepackages
{
	protected $filters;

	protected $domains;

	protected $menus;

	protected $geoCountries;

	protected $geoStates;

	protected $geoCities;

	protected $storages;

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

	protected function initGeoCountries()
	{
		$this->geoCountries = (new GeoCountries())->init();

		return $this->geoCountries;
	}

	protected function initGeoStates()
	{
		$this->geoStates = (new GeoStates())->init();

		return $this->geoStates;
	}

	protected function initGeoCities()
	{
		$this->geoCities = (new GeoCities())->init();

		return $this->geoCities;
	}

	protected function initStorages()
	{
		$this->storages = (new Storages())->init();

		return $this->storages;
	}

	protected function initAddressbook()
	{
		$this->addressbook = (new Addressbook())->init();

		return $this->addressbook;
	}
}