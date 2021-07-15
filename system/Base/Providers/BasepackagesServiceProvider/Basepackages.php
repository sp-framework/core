<?php

namespace System\Base\Providers\BasepackagesServiceProvider;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Address\Book as Addressbook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Address\Types as Addresstypes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\Email;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\EmailServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\EmailQueue;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Filters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCities;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCountries;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoStates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoTimezones;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Menus;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Notes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Notifications;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Processes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Accounts;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profile;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Roles;

class Basepackages
{
	protected $accounts;

	protected $roles;

	protected $profile;

	protected $email;

	protected $emailservices;

	protected $emailqueue;

	protected $filters;

	protected $domains;

	protected $menus;

	protected $geoCountries;

	protected $geoTimezones;

	protected $geoStates;

	protected $geoCities;

	protected $storages;

	protected $addressbook;

	protected $addresstypes;

	protected $activityLogs;

	protected $notes;

	protected $processes;

	protected $notifications;

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

	protected function initAccounts()
	{
		$this->accounts = (new Accounts())->init();

		return $this->accounts;
	}

	protected function initRoles()
	{
		$this->roles = (new Roles())->init();

		return $this->roles;
	}

	protected function initProfile()
	{
		$this->profile = (new Profile())->init();

		return $this->profile;
	}

	protected function initEmail()
	{
		$this->email = (new Email())->init();

		return $this->email;
	}

	protected function initEmailservices()
	{
		$this->emailservices = (new EmailServices())->init();

		return $this->emailservices;
	}

	protected function initEmailqueue()
	{
		$this->emailqueue = (new EmailQueue())->init();

		return $this->emailqueue;
	}

	protected function initFilters()
	{
		$this->filters = (new Filters())->init();

		return $this->filters;
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

	protected function initGeoTimezones()
	{
		$this->geoTimezones = (new GeoTimezones())->init();

		return $this->geoTimezones;
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

	protected function initAddresstypes()
	{
		$this->addresstypes = (new Addresstypes())->init();

		return $this->addresstypes;
	}

	protected function initActivityLogs()
	{
		$this->activityLogs = (new ActivityLogs())->init();

		return $this->activityLogs;
	}

	protected function initNotes()
	{
		$this->notes = (new Notes())->init();

		return $this->notes;
	}

	protected function initProcesses()
	{
		$this->processes = (new Processes())->init();

		return $this->processes;
	}

	protected function initNotifications()
	{
		$this->notifications = (new Notifications())->init();

		return $this->notifications;
	}
}