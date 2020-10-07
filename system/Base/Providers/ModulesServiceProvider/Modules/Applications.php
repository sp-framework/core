<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Di\DiInterface;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications as ApplicationsModel;

class Applications extends BasePackage
{
	protected $applications;

	protected $applicationInfo = null;

	protected $defaults = null;

	public function getApplicationInfo()
	{
		if (isset($this->applicationInfo)) {
			return $this->applicationInfo;
		} else {
			if ($this->checkApplicationRegistration($this->getApplicationName())) {
				return $this->applicationInfo;
			} else {
				// Throw Error
			}
		}
	}

	protected function getApplicationName()
	{
		$uri = $this->container->getShared('request')->getURI();

		$uri = explode('?', $uri);

		if ($uri[0] === '/') {
			if (!$this->defaults) {
				$this->getDefaults();

				if ($this->defaults) {
					return $this->defaults['application'];
				} else {
					return null;
				}
			} else {
				return null;
			}
		} else {
			return explode('/', $uri[0])[1];
		}
	}

	protected function getDefaults($name = null)
	{
		if ($name) {

			$namedApplication = $this->getNamedApplication($name);

			if ($namedApplication) {
				$application = $namedApplication;
			} else {
				$application = null;
			}

		} else if (!$this->defaults) {

			$defaultApplication = $this->getDefaultApplication();

			if ($defaultApplication) {
				$application = $defaultApplication;
			} else {
				$application = null;
			}

		}

		if (isset($application)) {
			$this->defaults['id'] = $application['id'];

			$this->defaults['application'] = $application['name'];

			$this->defaults['view'] = json_decode($application['settings'], true)['view'];

			$this->defaults['component'] = json_decode($application['settings'], true)['component'];

			return $this->defaults;
		}
	}

	public function getApplicationDefaults($name = null)
	{
		if (isset($this->defaults) && $name) {
			if ($name === $this->defaults['application']) {
				return $this->defaults;
			} else {
				return $this->getDefaults($name);
			}
		} else if (isset($this->defaults)) {
			return $this->defaults;
		}

		return $this->getDefaults($name);
	}

	protected function checkApplicationRegistration($name)
	{
		//First entry to check for application.
		$application = $this->getNamedApplication($name);

		$route = $this->getRouteApplication($name);

		if (count($application) > 0 || count($route) > 0) {
			if ($application) {
				$this->applicationInfo = $application;
			} else if ($route) {
				$this->applicationInfo = $route;
			}
			return true;
		} else {
			return false;
		}
	}

	public function getAllApplications()
	{
		$this->applications = ApplicationsModel::find(null, 'applications')->toArray();

		return $this;
	}

	protected function getNamedApplication($name)
	{
		return $this->applications
			[
				array_search(ucfirst($name), array_column($this->applications, 'name'))
			];
	}

	protected function getRouteApplication($route)
	{
		return $this->applications
			[
				array_search($route, array_column($this->applications, 'route'))
			];
	}

	protected function getDefaultApplication()
	{
		return $this->applications
			[
				array_search('1', array_column($this->applications, 'is_default'))
			];
	}
}