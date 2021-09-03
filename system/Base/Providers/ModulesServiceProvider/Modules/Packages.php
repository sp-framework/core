<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
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


	public function getNamedPackageForApp($name, $appId)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name, $appId) {
					$package = $package->toArray();
					$package['apps'] = Json::decode($package['apps'], true);
					if (isset($package['apps'][$appId])) {
						if (strtolower($package['name']) === strtolower($name) &&
							$package['apps'][$appId]['enabled'] === true
						) {
							return $package;
						}
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package name found for package ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamedPackageForRepo($name, $repo)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name, $repo) {
					$package = $package->toArray();

					if ($package['name'] === ucfirst($name) &&
						$package['repo'] === $repo
					) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package name found for package ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getIdPackage($id)
	{
		$filter =
			$this->model->filter(
				function($package) use ($id) {
					$package = $package->toArray();
					if ($package['id'] == $id) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package Id found for id ' . $id);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamePackage($name)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name) {
					$package = $package->toArray();
					if ($package['name'] === ucfirst($name)) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package found for name ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getPackagesForCategoryAndSubcategory($category, $subCategory, $inclCommon = true)
	{
		$packages = [];

		$filter =
			$this->model->filter(
				function($package) use ($category, $subCategory, $inclCommon) {
					$package = $package->toArray();
					if ($inclCommon) {
						if (($package['category'] === $category && $package['sub_category'] === $subCategory) ||
							($package['category'] === $category && $package['sub_category'] === 'common')
						) {
							return $package;
						}
					} else {
						if ($package['category'] === $category && $package['sub_category'] === $subCategory) {
							return $package;
						}
					}
				}
			);

		foreach ($filter as $key => $value) {
			$packages[$key] = $value;
		}
		return $packages;
	}

	public function getPackagesForAppType(string $type)
	{
		$packages = [];

		$filter =
			$this->model->filter(
				function($package) use ($type) {
					$package = $package->toArray();
					if ($package['app_type'] === $type) {
						return $package;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$packages[$key] = $value;
		}
		return $packages;
	}

	public function getPackagesForApp($appId)
	{
		$filter =
			$this->model->filter(
				function($package) use ($appId) {
					$package = $package->toArray();
					$package['apps'] = Json::decode($package['apps'], true);
					if (isset($package['apps'][$appId]['enabled']) &&
						$package['apps'][$appId]['enabled'] === true
					) {
						return $package;
					}
				}
			);

		return $filter;
	}

	public function addPackage(array $data)
	{
		if ($this->add($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Added ' . $data['name'] . ' package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error adding new package.';
		}
	}

	public function updatePackage(array $data)
	{
		if ($this->update($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating package.';
		}
	}

	public function removePackage(array $data)
	{
		if ($this->remove($data['id'])) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Removed package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error removing package.';
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

		// var_dump($subscriptions);
		$packages = $this->modules->packages->packages;

		foreach ($packages as $packageKey => $package) {
			if ($package['class'] && $package['class'] !== '') {
				// var_dump($package['notification_subscriptions']);
				if ($package['notification_subscriptions'] && $package['notification_subscriptions'] !== '') {
					$package['notification_subscriptions'] = Json::decode($package['notification_subscriptions'], true);

					foreach ($appsArr as $appKey => $app) {
						if (isset($subscriptions[$app['id']][$package['id']])) {
							// var_dump($package['name']);
							// var_dump($package['notification_subscriptions']);
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
										// var_dump('new Notification ' . $subscriptionKey);
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
								// var_dump('new App');
								if (is_array($subscriptions[$app['id']][$package['id']]) && count($subscriptions[$app['id']][$package['id']]) > 0) {
									$package['notification_subscriptions'][$app['id']] = [];
									// var_dump($subscriptions[$app['id']][$package['id']]);
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
											// var_dump($notificationKey);
											$package['notification_subscriptions'][$app['id']][$notificationKey] = [];
										}
									}
								}
							}
						}
					}
				} else {
					// var_dump('new Everything');
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
									// var_dump($notificationKey);
									$package['notification_subscriptions'][$app['id']][$notificationKey] = [];
								}
							}
						}
					}
				}
				// var_dump($package['notification_subscriptions']);
				$package['notification_subscriptions'] = Json::encode($package['notification_subscriptions']);
				$this->update($package);
			}
		}

		// if (count($subscriptions) > 0) {
		// 	foreach ($subscriptions as $appKey => $appNotifications) {
		// 		if (in_array($appKey, $appsIdArr)) {
		// 			if (count($appNotifications) > 0) {
		// 				foreach ($appNotifications as $packageId => $notifications) {

		// 					$package = $this->getIdPackage($packageId);
		// 					$notificationSubscriptions = [];

		// 					if ($package['notification_subscriptions'] && $package['notification_subscriptions'] !== '') {

		// 						$package['notification_subscriptions'] = Json::decode($package['notification_subscriptions'], true);

		// 						//unset if there is an leftover app from before.
		// 						foreach($package['notification_subscriptions'] as $appIdKey => $appIdNotifications) {
		// 							if (!in_array($appIdKey, $appsIdArr)) {
		// 								unset($package['notification_subscriptions'][$appIdKey]);
		// 							}
		// 						}

		// 						if (isset($package['notification_subscriptions'][$appKey])) {//Modify Notifications
		// 							foreach ($notifications as $notificationKey => $notification) {
		// 								if (isset($package['notification_subscriptions'][$appKey][$notificationKey])) {
		// 									if (in_array($account['id'], $package['notification_subscriptions'][$appKey][$notificationKey])) {
		// 										if ($notification == 0) {
		// 											unset($package['notification_subscriptions'][$appKey][$notificationKey][array_keys($package['notification_subscriptions'][$appKey][$notificationKey], $account['id'])[0]]);
		// 										}
		// 									} else {
		// 										if ($notification == 1) {
		// 											array_push($package['notification_subscriptions'][$appKey][$notificationKey], $account['id']);
		// 										}
		// 									}
		// 								} else {//If new notificationkey

		// 									$reflector = $this->annotations->get($package['class']);
		// 									$methods = $reflector->getMethodsAnnotations();

		// 									if ($methods) {
		// 										$notification_actions = [];
		// 										foreach ($methods as $annotation) {
		// 											array_push($notification_actions, $annotation->getAll('notification')[0]->getArguments()['name']);
		// 											$notification_allowed_methods = $annotation->getAll('notification_allowed_methods');
		// 											if (count($notification_allowed_methods) > 0) {
		// 												$notification_allowed_methods = $annotation->getAll('notification_allowed_methods')[0]->getArguments();
		// 											}
		// 										}

		// 										if (in_array($notificationKey, $notification_actions) || in_array($notificationKey, $notification_allowed_methods)) {
		// 											if ($notification == 1) {
		// 												$package['notification_subscriptions'][$appKey][$notificationKey] = [$account['id']];
		// 											} else {
		// 												$package['notification_subscriptions'][$appKey][$notificationKey] = [];
		// 											}
		// 										}
		// 									}
		// 								}
		// 							}

		// 							$package['notification_subscriptions'] = Json::encode($package['notification_subscriptions']);

		// 							$this->update($package);
		// 						} else {//New App Notifications
		// 							if (is_array($notifications) && count($notifications) > 0) {
		// 								$package['notification_subscriptions'][$appKey] = [];
		// 								foreach ($notifications as $notificationKey => $notification) {
		// 									if ($notification == 1) {
		// 										$package['notification_subscriptions'][$appKey][$notificationKey] = [$account['id']];
		// 									} else {
		// 										$package['notification_subscriptions'][$appKey][$notificationKey] = [];
		// 									}
		// 								}

		// 								$package['notification_subscriptions'] = Json::encode($package['notification_subscriptions']);

		// 								$this->update($package);
		// 							}
		// 						}
		// 					} else {//New All Notifications
		// 						if (is_array($notifications) && count($notifications) > 0) {
		// 							$notificationSubscriptions[$appKey] = [];

		// 							foreach ($notifications as $notificationKey => $notification) {
		// 								if ($notification == 1) {
		// 									$notificationSubscriptions[$appKey][$notificationKey] = [$account['id']];
		// 								} else {
		// 									$notificationSubscriptions[$appKey][$notificationKey] = [];
		// 								}
		// 							}
		// 						}
		// 						$packageSubscriptions['id'] = $packageId;
		// 						$packageSubscriptions['notification_subscriptions'] = Json::encode($notificationSubscriptions);

		// 						$this->update($packageSubscriptions);
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }

		// die();

	}
}