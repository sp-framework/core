<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesPackages;

class Packages extends BasePackage
{
	protected $modelToUse = ModulesPackages::class;

	public $packages;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getPackageByNameForAppId($name, $appId = null)
	{
		if (!$appId) {
			$appId = isset($this->apps->getAppInfo()['id']) ? $this->apps->getAppInfo()['id'] : false;

			if (!$appId) {
				return false;
			}
		}

		foreach($this->packages as $package) {
			$package['apps'] = $this->helper->decode($package['apps'], true);

			if (isset($package['apps'][$appId])) {
				if (strtolower($package['name']) === strtolower($name) &&
					$package['apps'][$appId]['enabled'] === true
				) {
					return $package;
				}
			}
		}

		return false;
	}

	public function getPackageByClassForAppId($class, $appId = null)
	{
		if (!$appId) {
			$appId = isset($this->apps->getAppInfo()['id']) ? $this->apps->getAppInfo()['id'] : false;

			if (!$appId) {
				return false;
			}
		}

		foreach($this->packages as $package) {
			$package['apps'] = $this->helper->decode($package['apps'], true);

			if ($package['class'] !== $class) {
				continue;
			}

			if (isset($package['apps'][$appId])) {
				if (isset($package['apps'][$appId]['enabled']) &&
					$package['apps'][$appId]['enabled'] === true
				) {
					return $package;
				}
			}
		}

		return false;
	}


	public function getPackagesByApiId($apiId)
	{
		$packages = [];

		foreach($this->packages as $package) {
			if ($package['api_id'] == $apiId) {
				array_push($packages, $package);
			}
		}

		return $packages;
	}

	public function getPackageByRepo($repo)
	{
		foreach($this->packages as $package) {
			if ($package['repo'] == $repo) {
				return $package;
			}
		}

		return false;
	}

	public function getPackageByAppTypeAndRepoAndClass($appType, $repo, $class)
	{
		foreach($this->packages as $package) {
			if ($package['app_type'] === $appType &&
				$package['repo'] === $repo &&
				trim($class, '\\') === trim($package['class'], '\\')
			) {
				return $package;
			}
		}

		return false;
	}

	public function getPackageByAppTypeAndName($appType, $name)
	{
		foreach($this->packages as $package) {
			if ($package['app_type'] === $appType &&
				$package['name'] === $name
			) {
				return $package;
			}
		}

		return false;
	}

	public function getPackageByNameForRepo($name, $repo)
	{
		foreach($this->packages as $package) {
			if (strtolower($package['name']) === strtolower($name) &&
				strtolower($package['repo']) === strtolower($repo)
			) {
				return $package;
			}
		}

		return false;
	}

	public function getPackageById($id)
	{
		foreach($this->packages as $package) {
			if ($package['id'] == $id) {
				return $package;
			}
		}

		return false;
	}

	public function getPackageByName($name)
	{
		foreach($this->packages as $package) {
			if (strtolower($package['name']) === strtolower($name)) {
				return $package;
			}
		}

		return false;
	}

	public function getPackagesForCategory($category)
	{
		$packages = [];

		foreach($this->packages as $package) {
			if ($package['category'] === $category) {
				$packages[$package['id']] = $package;
			}
		}

		return $packages;
	}

	public function getPackagesForAppType($appType)
	{
		$packages = [];

		foreach($this->packages as $package) {
			if ($package['app_type'] === $appType) {
				$packages[$package['id']] = $package;
			}
		}

		return $packages;
	}

	public function getPackagesForAppId($appId)
	{
		$packages = [];

		foreach($this->packages as $package) {
			$package['apps'] = $this->helper->decode($package['apps'], true);

			if (isset($package['apps'][$appId]['enabled']) &&
				$package['apps'][$appId]['enabled'] == 'true'
			) {
				$packages[$package['id']] = $package;
			}
		}

		return $packages;
	}

	public function addPackage(array $data)
	{
		if ($this->add($data)) {
			$this->addResponse('Added ' . $data['name'] . ' package');
		} else {
			$this->addResponse('Error adding new package.', 1);
		}
	}

	public function updatePackages(array $data)
	{
		if ($this->update($data)) {
			$this->addResponse('Updated ' . $data['name'] . ' package');
		} else {
			$this->addResponse('Error updating package.', 1);
		}
	}

	public function removePackage(array $data)
	{
		if ($this->remove($data['id'])) {
			$this->addResponse('Removed package');
		} else {
			$this->addResponse('Error removing package.',1);
		}
	}

	public function updateNotificationSubscriptions(array $subscriptions)
	{
		$account = $this->auth->account();

		if (!$account) {
			return;
		}

		foreach ($this->apps->apps as $appId => $app) {
			if (!isset($subscriptions[$appId]) ||
				!isset($subscriptions[$appId]['packages'])
			) {
				continue;
			}

			foreach ($subscriptions[$appId]['packages'] as $packageId => $packageSubscriptions) {
				if (!isset($this->packages[$packageId])) {
					continue;
				}

				if ($this->packages[$packageId]['notification_subscriptions']) {
					if (!is_array($this->packages[$packageId]['notification_subscriptions'])) {
						$this->packages[$packageId]['notification_subscriptions'] = $this->helper->decode($this->packages[$packageId]['notification_subscriptions'], true);
					}

					if (isset($this->packages[$packageId]['notification_subscriptions'][$appId])) {
						foreach ($packageSubscriptions as $subscriptionKey => $subscriptionValue) {
							if (isset($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey])) {
								if ($subscriptionValue == 1) {
									if ($subscriptionKey == 'email') {
										if (!isset($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][$account['id']])) {
											$this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][$account['id']] = $account['email'];
										}
									} else {
										if (!in_array($account['id'], $this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey])) {
											array_push($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey], $account['id']);
										}
									}
								} else if ($subscriptionValue == 0) {
									if ($subscriptionKey == 'email') {
										if (isset($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][$account['id']])) {
											unset($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][$account['id']]);
										}
									} else {
										if (in_array($account['id'], $this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey])) {
											unset($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][array_keys($this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey], $account['id'])[0]]);
										}
									}
								}
							} else {//If new notificationkey
								$reflector = $this->annotations->get($this->packages[$packageId]['class']);
								$methods = $reflector->getMethodsAnnotations();

								if ($methods) {
									$notification_actions = [];
									foreach ($methods as $annotation) {
										array_push($notification_actions, $annotation->getAll('notification')[0]->getArguments()['name']);
										$notification_allowed_methods = $annotation->getAll('notification_allowed_methods');
										if (count($notification_allowed_methods) > 0) {
											$notification_allowed_methods = $annotation->getAll('notification_allowed_methods')[0]->getArguments();
										}
									}

									if (in_array($subscriptionKey, $notification_actions)) {
										if ($subscriptionValue == 1) {
											$this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey] = [$account['id']];
										} else {
											$this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey] = [];
										}
									}

									if (in_array($subscriptionKey, $notification_allowed_methods)) {
										if ($subscriptionValue == 1) {
											if ($subscriptionKey == 'email') {
												$this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey][$account['id']] = [$account['email']];
											}
										} else {
											$this->packages[$packageId]['notification_subscriptions'][$appId][$subscriptionKey] = [];
										}
									}
								}
							}
						}
					} else {
						$this->packages[$packageId]['notification_subscriptions'][$appId] = [];

						foreach ($packageSubscriptions as $notificationKey => $notification) {
							if ($notification == 1) {
								if ($notificationKey == 'email') {
									$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [$account['id'] => $account['email']];
								} else {
									$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [$account['id']];
								}
							} else {
								$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [];
							}
						}
					}
				} else {
					$this->packages[$packageId]['notification_subscriptions'][$appId] = [];

					foreach ($packageSubscriptions as $notificationKey => $notification) {
						if ($notification == 1) {
							if ($notificationKey == 'email') {
								$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [$account['id'] => $account['email']];
							} else {
								$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [$account['id']];
							}
						} else {
							$this->packages[$packageId]['notification_subscriptions'][$appId][$notificationKey] = [];
						}
					}
				}

				$this->update($this->packages[$packageId]);
			}
		}
	}

	public function msupdate(array $data)//module settings update
	{
		$package = $this->getById($data['id']);

		if (is_string($package['settings'])) {
			$package['settings'] = $this->helper->decode($package['settings'], true);
		}

		foreach ($data as $key => $settingsData) {
			if ($key !== 'id' &&
				$key !== 'module_type' &&
				$settingsData !== $this->security->getRequestToken()
			) {
				if (isset($package['settings'][$key])) {
					$settingsData = $this->helper->decode($settingsData, true);

					$package['settings'][$key] = $settingsData;
				}
			}
		}

		try {
			$packageReflection = new \ReflectionClass($package['class']);
			$packageSettingsProperty = $packageReflection->getProperty('settings');
			$settingsClass = $packageSettingsProperty->getValue(new $package['class']);

			$settingsClass = new $settingsClass;
			if (method_exists($settingsClass, 'beforeUpdate')) {
				$settingsClass->beforeUpdate($this, $package, $data);
			}

			$this->update($package);

			if (method_exists($settingsClass, 'afterUpdate')) {
				$settingsClass->afterUpdate($this, $package, $data);
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return true;
	}
}