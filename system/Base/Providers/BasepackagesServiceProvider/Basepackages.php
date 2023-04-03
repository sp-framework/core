<?php

namespace System\Base\Providers\BasepackagesServiceProvider;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\AddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Dashboards;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\Email;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\EmailQueue;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\EmailServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Filters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCities;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCountries;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoStates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoTimezones;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ImportExport;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Menus;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Notes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Notifications;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Progress;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Pusher;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Templates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Accounts;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profile;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Roles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Widgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

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

	protected $activityLogs;

	protected $notes;

	protected $workers;

	protected $notifications;

	protected $pusher;

	protected $importexport;

	protected $templates;

	protected $dashboards;

	protected $widgets;

	protected $progress;

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
		$this->addressbook = (new AddressBook())->init();

		return $this->addressbook;
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

	protected function initNotifications()
	{
		$this->notifications = (new Notifications())->init();

		return $this->notifications;
	}

	protected function initPusher()
	{
		$this->pusher = (new Pusher())->init();

		return $this->pusher;
	}

	protected function initWorkers()
	{
		$this->workers = (new Workers())->init();

		return $this->workers;
	}

	protected function initImportExport()
	{
		$this->importexport = (new ImportExport())->init();

		return $this->importexport;
	}

	protected function initTemplates()
	{
		$this->templates = (new Templates())->init();

		return $this->templates;
	}

	protected function initDashboards()
	{
		$this->templates = (new Dashboards())->init();

		return $this->templates;
	}

	protected function initWidgets()
	{
		$this->templates = (new Widgets())->init();

		return $this->templates;
	}

	protected function initProgress()
	{
		$this->progress = (new Progress())->init();

		return $this->progress;
	}
}