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

	public function getPackageByNameForAppId($name, $appId)
	{
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

		$appsIdArr = [];

		$appsArr = $this->apps->apps;

		if (count($appsArr) > 0) {
			foreach ($appsArr as $key => $value) {
				array_push($appsIdArr, $value['id']);
			}
		} else {
			return;
		}

		foreach ($this->packages as $packageKey => $package) {
			if ($package['class'] && $package['class'] !== '') {
				if ($package['notification_subscriptions'] && $package['notification_subscriptions'] !== '') {
					$package['notification_subscriptions'] = $this->helper->decode($package['notification_subscriptions'], true);

					foreach ($appsArr as $appKey => $app) {
						if (isset($subscriptions[$app['id']][$package['id']])) {
							if (isset($package['notification_subscriptions'][$app['id']])) {
								foreach ($subscriptions[$app['id']][$package['id']] as $subscriptionKey => $subscriptionValue) {
									if (isset($package['notification_subscriptions'][$app['id']][$subscriptionKey])) {
										if ($subscriptionValue == 1) {
											if ($subscriptionKey == 'email') {
												if (!isset($package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']])) {
													$package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']] = $account['email'];
												}
											} else if ($subscriptionKey == 'sms') {
												if (!isset($package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']])) {
													$package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']] = $account['profile']['contact_mobile'];
												}
											} else {
												if (!in_array($account['id'], $package['notification_subscriptions'][$app['id']][$subscriptionKey])) {
													array_push($package['notification_subscriptions'][$app['id']][$subscriptionKey], $account['id']);
												}
											}
										} else if ($subscriptionValue == 0) {
											if ($subscriptionKey == 'email' || $subscriptionKey == 'sms') {
												if (isset($package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']])) {
													unset($package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']]);
												}
											} else {
												if (in_array($account['id'], $package['notification_subscriptions'][$app['id']][$subscriptionKey])) {
													unset($package['notification_subscriptions'][$app['id']][$subscriptionKey][array_keys($package['notification_subscriptions'][$app['id']][$subscriptionKey], $account['id'])[0]]);
												}
											}
										}
									} else {//If new notificationkey
										$reflector = $this->annotations->get($package['class']);
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
													$package['notification_subscriptions'][$app['id']][$subscriptionKey] = [$account['id']];
												} else {
													$package['notification_subscriptions'][$app['id']][$subscriptionKey] = [];
												}
											}

											if (in_array($subscriptionKey, $notification_allowed_methods)) {
												if ($subscriptionValue == 1) {
													if ($subscriptionKey == 'email') {
														$package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']] = [$account['email']];
													} else if ($subscriptionKey == 'sms') {
														$package['notification_subscriptions'][$app['id']][$subscriptionKey][$account['id']] = [$account['profile']['contact_mobile']];
													}
												} else {
													$package['notification_subscriptions'][$app['id']][$subscriptionKey] = [];
												}
											}
										}
									}
								}
							} else {
								if (is_array($subscriptions[$app['id']][$package['id']]) && count($subscriptions[$app['id']][$package['id']]) > 0) {
									$package['notification_subscriptions'][$app['id']] = [];

									foreach ($subscriptions[$app['id']][$package['id']] as $notificationKey => $notification) {
										if ($notification == 1) {
											if ($notificationKey == 'email') {
												$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id'] => $account['email']];
											} else if ($notificationKey == 'sms') {
												$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id'] => $account['profile']['contact_mobile']];
											} else {
												$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id']];
											}
										} else {
											$package['notification_subscriptions'][$app['id']][$notificationKey] = [];
										}
									}
								}
							}
						}
					}
				} else {
					foreach ($appsArr as $appKey => $app) {
						if (isset($subscriptions[$app['id']][$package['id']])) {
							$package['notification_subscriptions'][$app['id']] = [];

							foreach ($subscriptions[$app['id']][$package['id']] as $notificationKey => $notification) {
								if ($notification == 1) {
									if ($notificationKey == 'email') {
										$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id'] => $account['email']];
									} else if ($notificationKey == 'sms') {
										$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id'] => $account['profile']['contact_mobile']];
									} else {
										$package['notification_subscriptions'][$app['id']][$notificationKey] = [$account['id']];
									}
								} else {
									$package['notification_subscriptions'][$app['id']][$notificationKey] = [];
								}
							}
						}
					}
				}
				$package['notification_subscriptions'] = $this->helper->encode($package['notification_subscriptions']);

				$this->update($package);
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

		$package['settings'] = $this->helper->encode($package['settings']);

		$this->update($package);
	}
}