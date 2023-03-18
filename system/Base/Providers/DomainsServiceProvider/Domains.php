<?php

namespace System\Base\Providers\DomainsServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\DomainsServiceProvider\Model\Domains as DomainsModel;

class Domains extends BasePackage
{
	protected $modelToUse = DomainsModel::class;

	protected $packageName = 'domains';

	protected $packageNameS = 'domain';

	public $domains;

	public $domain;

	protected $appDefaults;

	protected $defaults = null;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->getDomain();

		return $this;
	}

	public function getDomain()
	{
		if (!$this->domain) {
			$this->setDomain();

			return $this->domain;
		}
		return $this->domain;
	}

	protected function setDomain()
	{
		$this->domain = $this->get(['name' => $this->request->getHttpHost()]);

		if ($this->domain) {
			if ($this->domain['apps'] !== '') {
				$this->domain['apps'] = Json::decode($this->domain['apps'], true);
			}
		}
	}

	public function get(array $data = [], bool $resetCache = false)
	{
		if (count($data) === 0) {
			return $this->domains;
		}

		foreach($this->domains as $domain) {
			if (isset($data['id'])) {
				if ($domain['id'] === $data['id']) {
					return $domain;
				}
			}

			if (isset($data['name'])) {
				if ($domain['name'] === $data['name']) {
					return $domain;
				}
			}
		}

		return false;
	}

	/**
	 * @notification(name=add)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function add(array $data)
	{
		try {
			$add = $this->addToDb($data);
		} catch (\Exception $e) {
			if ($e->getCode() == '23000') {
				$this->addResponse('Domain name already in use.', 1);

				return;
			}
		}

		if ($add) {
			$this->addActivityLog($data);

			$this->addResponse('Added ' . $data['name'] . ' domain', 0, null, true);

			$this->addToNotification('add', 'Added new domain ' . $data['name'], null, $this->modules->packages->get(['name' => 'Domains']));
		} else {
			$this->addResponse('Error adding new domain.', 1, []);
		}
	}

	/**
	 * @notification(name=update)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function update(array $data)
	{
		$domain = $this->get(['id' => $data['id']]);

		$domain = array_merge($domain, $data);

		try {
			$update = $this->updateToDb($domain);
		} catch (\Exception $e) {
			if ($e->getCode() == '23000') {
				$this->addResponse('Domain name ' . $data['name'] . ' already in use.', 1);

				return;
			}
		}

		if ($update) {
			$this->addActivityLog($data, $domain);

			$this->addResponse('Updated domain ' . $data['name']);

			$this->addToNotification('update', 'Updated domain ' . $data['name'], null, $this->modules->packages->get(['name' => 'Domains']));
		} else {
			$this->addResponse('Error adding new domain.', 1);
		}
	}

	/**
	 * @notification(name=remove)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function remove(array $data)
	{
		$domain = $this->get(['id' => $data['id']]);

		if ($this->removeFromDb($domain['id'])) {
			$this->addResponse('Removed domain ' . $domain['name']);

			$this->addToNotification('remove', 'Removed domain ' . $domain['name'], null, $this->modules->packages->get(['name' => 'Domains']));
		} else {
			$this->addResponse('Error removing domain.', 1);
		}
	}

	// public function getNamedDomain($name)
	// {
	// 	if (!$this->domains) {
	// 		$this->init();
	// 	}

	// 	foreach($this->domains as $domain) {
	// 		if ($domain['name'] === $name) {
	// 			return $domain;
	// 		}
	// 	}

	// 	return false;
	// }

	// public function getIdDomain($id)
	// {
	// 	if (!$this->domains) {
	// 		$this->init();
	// 	}

	// 	foreach($this->domains as $domain) {
	// 		if ($domain['id'] == $id) {
	// 			return $domain;
	// 		}
	// 	}

	// 	return false;
	// }

	public function generateViewData(int $did = null)
	{
		$appsArr = $this->apps->apps;
		$apps = [];

		foreach ($appsArr as $key => $value) {
			$apps[$value['id']] = $value;
			$apps[$value['id']]['views'] =
				$this->modules->views->get(['app_id' => $value['id']]);
		}

		$this->packagesData->apps = $apps;

		$this->packagesData->emailservices = $this->basepackages->emailservices->init()->emailServices;

		$this->packagesData->storages = $this->basepackages->storages->storages;

		if ($did) {
			$domain = $this->get(['id' => $did]);

			if (!$domain) {
				return false;
			}

			$domain['apps'] = Json::decode($domain['apps'], true);
			if ($domain['settings']) {
				$domain['settings'] = Json::decode($domain['settings'], true);
			} else {
				$domain['settings'] = [];
			}

			$this->packagesData->domain = $domain;

			return true;
		}
		return false;
	}

	public function removeAppFromApps(int $id)
	{
		foreach ($this->domains as $domainkey => $domain) {
			if (!is_array($domain['apps'])) {
				$domain['apps'] = Json::decode($domain['apps'], true);
			}

			if (isset($domain['apps'][$id])) {
				unset($domain['apps'][$id]);
			}

			$domain['apps'] = Json::encode($domain['apps']);

			$this->update($domain);
		}
	}
}